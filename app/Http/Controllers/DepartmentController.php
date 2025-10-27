<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\HandlesExports;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller
{
    use HandlesExports;
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Department::query();

        $allowedSorts = ['dept_id','dept_name','dept_code'];
        $sortBy = $request->input('sort_by', 'dept_name');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'dept_name';

        if ($search = $request->query('search')) {
            $query->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%");
        }

    $departments = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['dept_code','dept_name']);

        $validator = Validator::make($data, [
            'dept_code' => [
                'required','string','max:20',
                // unique only among non-deleted records so soft-deleted codes can be reused
                \Illuminate\Validation\Rule::unique('tbldepartment','dept_code')->where(function($query){
                    $query->where('is_deleted', 0);
                }),
            ],
            'dept_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'add', 'success' => false], 422);
        }
        // Duplicate check by dept_code among non-deleted records
        if (!empty($data['dept_code']) && Department::where('dept_code', $data['dept_code'])->where('is_deleted', 0)->exists()) {
            return response()->json(['message' => 'A department with that code already exists in records.', 'op' => 'add', 'success' => false], 409);
        }
        try {
            $dept = Department::create($data);
            \Log::info('Department created: ' . $dept->dept_id);
            // Try to render a single-row partial so the frontend can insert it; if rendering fails, log and return success without row_html
            $rowHtml = null;
            try{
                $rowHtml = view('departments._row', ['dept' => $dept])->render();
            } catch (\Throwable $e) {
                \Log::error('Failed to render department row partial: ' . $e->getMessage());
            }
            $payload = ['message' => 'Department created', 'op' => 'add', 'success' => true, 'data' => $dept];
            if($rowHtml) $payload['row_html'] = $rowHtml;
            return response()->json($payload);
        } catch (\Exception $e) {
            \Log::error('Department create failed: ' . $e->getMessage());
            return response()->json(['message' => 'Department create failed', 'op' => 'add', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $data = $request->only(['dept_code','dept_name']);

        $validator = Validator::make($data, [
            'dept_code' => [
                'required','string','max:20',
                \Illuminate\Validation\Rule::unique('tbldepartment','dept_code')->ignore($dept->dept_id, 'dept_id')->where(function($query){
                    $query->where('is_deleted', 0);
                }),
            ],
            'dept_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'op' => 'update', 'success' => false], 422);
        }
        try {
            $dept->update($data);
            return response()->json(['message' => 'Department updated', 'op' => 'update', 'success' => true, 'data' => $dept]);
        } catch (\Exception $e) {
            \Log::error('Department update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Department update failed', 'op' => 'update', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        try {
            // Make dept_code unique before soft delete to avoid unique constraint error
            $originalCode = (string) $dept->dept_code;
            $suffix = '_deleted_' . time();
            $maxLen = 20; // dept_code column is varchar(20)
            if (strlen($originalCode) + strlen($suffix) > $maxLen) {
                $trunc = substr($originalCode, 0, $maxLen - strlen($suffix));
            } else {
                $trunc = $originalCode;
            }
            $dept->dept_code = $trunc . $suffix;
            $dept->is_deleted = 1;
            $dept->save();
            return response()->json(['message' => 'Department deleted', 'op' => 'delete', 'success' => true]);
        } catch (\Exception $e) {
            $msg = 'Department delete failed';
            if ($e->getMessage()) {
                $msg .= ': ' . $e->getMessage();
            }
            \Log::error($msg);
            return response()->json(['message' => $msg, 'op' => 'delete', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // Get records sorted by ID ascending
        $items = $this->getOrderedRecords(Department::class);
        
        try {
            // Try to use Maatwebsite Excel export
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\DepartmentExport($items);
                return Excel::download($export, $this->getExportFilename('departments', 'xlsx'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
        }

        // Fallback to CSV if Excel export fails
        $headers = ['ID', 'Department Code', 'Department Name'];
        return $this->downloadCsv('departments', function($file) use ($items, $headers) {
            fputcsv($file, $headers);
            foreach ($items as $record) {
                fputcsv($file, [
                    $record->dept_id,
                    $record->dept_code,
                    $record->dept_name,
                ]);
            }
        }, 'Department Records');
    }

    public function exportPDF(Request $request)
    {
        // Get records sorted by ID ascending
        $items = $this->getOrderedRecords(Department::class);
        
        // Prepare view data
        $viewData = [
            'items' => $items,
            'headers' => ['ID', 'Code', 'Name'],
            'columns' => ['dept_id', 'dept_code', 'dept_name'],
            'logoDataUri' => $this->getLogoDataUri()
        ];
        
        // Load PDF view
        $pdf = Pdf::loadView('departments.export_pdf', $viewData);
        
        // Apply standard footer with page numbers
        $this->applyPdfFooter($pdf);
        
        // Generate filename and download
        return $pdf->download($this->getExportFilename('departments', 'pdf'));
    }
}
