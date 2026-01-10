<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

trait HandlesBackupRestore
{
    public function backup(Request $request)
    {
        \Log::info("Starting backup for: " . $this->getResourceName());
        
        $modelClass = $this->getModelClass();
        $resourceName = $this->getResourceName();

        // Get all records
        $records = $this->getOrderedRecords($modelClass, $this->getRelations());
        \Log::info("Found {$records->count()} records for {$resourceName}");

        // Convert to array with relations loaded
        $data = $records->map(function ($record) {
            return $record->toArray();
        });

        // Create backup data structure
        $backupData = [
            'resource' => $resourceName,
            'timestamp' => now()->toISOString(),
            'count' => $data->count(),
            'data' => $data
        ];

        // Generate filename
        $filename = sprintf('%s_backup_%s.json', $resourceName, date('Y-m-d_H-i-s'));
        \Log::info("Generated filename: {$filename}");

        // Store in storage/app/backups
        $path = "backups/{$filename}";
        $jsonContent = json_encode($backupData, JSON_PRETTY_PRINT);
        \Log::info("JSON content length: " . strlen($jsonContent));
        
        // Test file creation
        $testPath = "backups/test_{$resourceName}.txt";
        Storage::disk('local')->put($testPath, "Test file for {$resourceName}");
        
        $result = Storage::disk('local')->put($path, $jsonContent);
        
        \Log::info("Backup attempt: {$resourceName}, path: {$path}, result: " . ($result ? 'success' : 'failed'));
        
        if (!$result) {
            \Log::error("Backup failed to write file: {$path}");
            return redirect()->back()->with('backup_error', 'Failed to save backup file');
        }

        // Verify file exists
        if (!Storage::disk('local')->exists($path)) {
            \Log::error("File was not created: {$path}");
            return redirect()->back()->with('backup_error', 'Backup file was not created');
        }

        \Log::info("Backup successful: {$path} exists");
        return redirect()->back()->with('backup_success', [
            'message' => ucfirst($resourceName) . ' data backed up successfully',
            'filename' => $filename,
            'location' => 'storage/app/backups/',
            'count' => $data->count()
        ]);
    }

    /**
     * Restore data from JSON file
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json|max:51200', // 50MB max
            'mode' => 'required|in:skip,update,replace'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'success' => false
            ], 422);
        }

        $modelClass = $this->getModelClass();
        $resourceName = $this->getResourceName();

        try {
            // Read uploaded file
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $backupData = json_decode($content, true);

            if (!$backupData || !isset($backupData['data'])) {
                return response()->json([
                    'message' => 'Invalid backup file format',
                    'success' => false
                ], 400);
            }

            if ($backupData['resource'] !== $resourceName) {
                return response()->json([
                    'message' => 'Backup file is for a different resource',
                    'success' => false
                ], 400);
            }

            $mode = $request->input('mode');
            $results = $this->performRestore($modelClass, $backupData['data'], $mode);

            return response()->json([
                'message' => ucfirst($resourceName) . ' data restored successfully',
                'results' => $results,
                'success' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Restore failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Restore failed: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * List available backup files
     */
    public function listBackups()
    {
        $resourceName = $this->getResourceName();
        $files = Storage::disk('local')->files('backups');

        $backups = collect($files)
            ->filter(function ($file) use ($resourceName) {
                return str_starts_with(basename($file), $resourceName . '_backup_');
            })
            ->map(function ($file) {
                $content = Storage::disk('local')->get($file);
                $data = json_decode($content, true);

                return [
                    'filename' => basename($file),
                    'timestamp' => $data['timestamp'] ?? null,
                    'count' => $data['count'] ?? 0,
                    'size' => Storage::disk('local')->size($file)
                ];
            })
            ->sortByDesc('timestamp')
            ->values();

        return response()->json([
            'backups' => $backups,
            'success' => true
        ]);
    }

    /**
     * Download a backup file
     */
    public function downloadBackup($filename)
    {
        $resourceName = $this->getResourceName();

        if (!str_starts_with($filename, $resourceName . '_backup_')) {
            return response()->json([
                'message' => 'Invalid backup file',
                'success' => false
            ], 400);
        }

        $path = "backups/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'message' => 'Backup file not found',
                'success' => false
            ], 404);
        }

        return Storage::disk('local')->download($path);
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup($filename)
    {
        $resourceName = $this->getResourceName();

        if (!str_starts_with($filename, $resourceName . '_backup_')) {
            return response()->json([
                'message' => 'Invalid backup file',
                'success' => false
            ], 400);
        }

        $path = "backups/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'message' => 'Backup file not found',
                'success' => false
            ], 404);
        }

        Storage::disk('local')->delete($path);

        return response()->json([
            'message' => 'Backup file deleted successfully',
            'success' => true
        ]);
    }

    /**
     * Perform the actual restore operation
     */
    protected function performRestore($modelClass, array $data, string $mode)
    {
        $results = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];

            foreach ($data as $item) {
                $results['processed']++;

                // Start a transaction per-item so a single failing item doesn't roll back the whole batch
                DB::beginTransaction();
                try {
                    $model = new $modelClass;
                    $keyName = $model->getKeyName();

                    if ($keyName && isset($item[$keyName])) {
                        // Model with single primary key
                        $keyValue = $item[$keyName];
                        $existing = $modelClass::query()->withoutGlobalScope('is_deleted')->find($keyValue);

                        if ($existing) {
                            if ($existing->is_deleted == 1) {
                                // If soft deleted, try to restore it first. If a unique constraint prevents
                                // restoring (duplicate active value), attempt to resolve based on mode.
                                try {
                                    $existing->restore();
                                } catch (\Exception $e) {
                                    $sqlState = method_exists($e, 'getCode') ? $e->getCode() : null;
                                    $msg = $e->getMessage();
                                    if ($sqlState == '23000' || str_contains($msg, 'Duplicate entry')) {
                                        // Heuristic: handle common unique columns for students (student_no, email)
                                        if (isset($item['student_no'])) {
                                            $conflict = $modelClass::where('student_no', $item['student_no'])->where('is_deleted', 0)->first();
                                            if ($conflict) {
                                                if ($mode === 'replace') {
                                                    // soft-delete the conflicting active record so we can restore
                                                    $conflict->is_deleted = 1;
                                                    $conflict->save();
                                                    // retry restore
                                                    $existing->restore();
                                                } else {
                                                    // skip restoring to avoid duplicate constraint
                                                    $results['skipped']++;
                                                    DB::rollBack();
                                                    continue;
                                                }
                                            } else {
                                                // No identifiable conflict - treat as error
                                                $results['errors']++;
                                                DB::rollBack();
                                                continue;
                                            }
                                        } elseif (isset($item['email'])) {
                                            $conflict = $modelClass::where('email', $item['email'])->where('is_deleted', 0)->first();
                                            if ($conflict) {
                                                if ($mode === 'replace') {
                                                    $conflict->is_deleted = 1;
                                                    $conflict->save();
                                                    $existing->restore();
                                                } else {
                                                    $results['skipped']++;
                                                    DB::rollBack();
                                                    continue;
                                                }
                                            } else {
                                                $results['errors']++;
                                                DB::rollBack();
                                                continue;
                                            }
                                        } else {
                                            // Unknown duplicate, record error
                                            $results['errors']++;
                                            DB::rollBack();
                                            continue;
                                        }
                                    } else {
                                        // not a duplicate constraint - rethrow for outer handler
                                        throw $e;
                                    }
                                }
                            }
                            if ($mode === 'skip') {
                                $results['skipped']++;
                                DB::rollBack();
                                continue;
                            } elseif ($mode === 'update' || $mode === 'replace') {
                                $existing->update($item);
                                $results['updated']++;
                            }
                        } else {
                            $modelClass::create($item);
                            $results['created']++;
                        }
                    } else {
                        // Model without primary key or composite key
                        // For CoursePrerequisite, check for existing
                        if ($resourceName === 'course-prerequisites') {
                            $existing = $modelClass::where('course_id', $item['course_id'] ?? null)
                                                   ->where('prereq_course_id', $item['prereq_course_id'] ?? null)
                                                   ->first();
                            if ($existing) {
                                if ($mode === 'skip') {
                                    $results['skipped']++;
                                    DB::rollBack();
                                    continue;
                                } elseif ($mode === 'update' || $mode === 'replace') {
                                    $existing->update($item);
                                    $results['updated']++;
                                    DB::commit();
                                    continue;
                                }
                            }
                        }
                        // For now, just try to create (may fail if unique constraints)
                        try {
                            $modelClass::create($item);
                            $results['created']++;
                        } catch (\Exception $e) {
                            if ($mode === 'skip') {
                                $results['skipped']++;
                                DB::rollBack();
                                continue;
                            } else {
                                $results['errors']++;
                                DB::rollBack();
                                continue;
                            }
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Restore item failed: ' . $e->getMessage() . ' Item:' . json_encode($item));
                    $results['errors']++;
                    // continue with next item
                    continue;
                }
            }

        return $results;
    }

    /**
     * Get the model class for this controller
     * Should be overridden in each controller
     */
    abstract protected function getModelClass();

    /**
     * Get the resource name (e.g., 'students', 'courses')
     * Should be overridden in each controller
     */
    abstract protected function getResourceName();

    /**
     * Get relations to load for backup
     * Can be overridden in controllers that need relations
     */
    protected function getRelations(): array
    {
        return [];
    }
}