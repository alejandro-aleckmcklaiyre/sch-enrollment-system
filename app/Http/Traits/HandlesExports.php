<?php

namespace App\Http\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HandlesExports
{
    /**
     * Get records ordered by primary key ascending
     */
    protected function getOrderedRecords($model, $with = []): Collection
    {
        $query = $model::query();
        if (!empty($with)) {
            $query->with($with);
        }
        
        // Add soft delete condition if the model has it
        if (method_exists($model, 'getDeletedAtColumn')) {
            $query->where(function($q) {
                $q->where('is_deleted', 0)->orWhereNull('is_deleted');
            });
        }

        // Use model's primary key or getKeyName() method
        $keyName = (new $model)->getKeyName();
        return $query->orderBy($keyName, 'asc')->get();
    }

    /**
     * Apply standard PDF footer with page numbers
     */
    protected function applyPdfFooter($pdf)
    {
        $pdf->render();
        try {
            $dompdf = $pdf->getDomPDF();
            if ($dompdf && ($canvas = $dompdf->get_canvas()) && method_exists($canvas, 'page_text')) {
                $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');
                $size = 9;
                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                $width = $dompdf->getFontMetrics()->get_text_width(
                    str_replace(['{PAGE_NUM}', '{PAGE_COUNT}'], ['99', '99'], $text),
                    $font,
                    $size
                );
                
                // Center the text
                $x = ($canvas->get_width() - $width) / 2;
                $y = $canvas->get_height() - 20;
                
                $canvas->page_text($x, $y, $text, $font, $size, array(0,0,0));
            }
        } catch (\Throwable $ex) {
            \Log::warning('Failed to inject PDF footer: ' . $ex->getMessage());
        }
    }

    /**
     * Generate standardized export filename
     */
    protected function getExportFilename(string $prefix, string $extension): string
    {
        return sprintf('%s_%s.%s', $prefix, date('Ymd_His'), $extension);
    }

    /**
     * Load logo data URI for PDF exports
     */
    protected function getLogoDataUri(): ?string
    {
        // Try multiple possible logo paths
        $possiblePaths = [
            public_path('images/pup_logo.jpg'),
            public_path('image/pup_logo.jpg'),
            public_path('pup_logo.jpg')
        ];
        
        foreach ($possiblePaths as $logoPath) {
            if (!file_exists($logoPath)) {
                continue;
            }

            try {
                if (@getimagesize($logoPath) !== false && filesize($logoPath) > 512) {
                    $mime = mime_content_type($logoPath) ?: 'image/jpeg';
                    $data = base64_encode(file_get_contents($logoPath));
                    if ($data) {
                        return 'data:' . $mime . ';base64,' . $data;
                    }
                }
            } catch (\Throwable $ex) {
                \Log::warning('Failed to load logo from ' . $logoPath . ': ' . $ex->getMessage());
            }
        }

        return null;
    }

    /**
     * Write standardized CSV header rows
     * @param resource $file Open file handle to write to
     * @param string $reportTitle Title of the report (e.g. 'Room Records')
     */
    protected function writeCsvHeader($file, string $reportTitle)
    {
        // Write institutional header
        fputcsv($file, ['POLYTECHNIC UNIVERSITY OF THE PHILIPPINES']);
        fputcsv($file, ['Taguig Campus']);
        fputcsv($file, ['Date Created: ' . now()->format(config('export.date_format'))]);
        // Write the report title (e.g. "Course Records") on its own row so CSV
        // consumers can clearly see what type of records follow.
        fputcsv($file, [strtoupper($reportTitle)]);
        fputcsv($file, []); // Blank line after header
    }

    /**
     * Get filtered and sorted records for export
     */
    protected function getFilteredRecordsForExport($request, $query, $model, string $defaultSortField = null)
    {
        // Add soft delete condition if the model has it
        if (method_exists($model, 'getDeletedAtColumn')) {
            $query->where(function($q) {
                $q->where('is_deleted', 0)->orWhereNull('is_deleted');
            });
        }

        // Filter by search if provided
        if ($search = $request->input('search')) {
            // Let the model define search columns
            if (method_exists($model, 'getSearchableColumns')) {
                $searchColumns = $model::getSearchableColumns();
                $query->where(function($q) use ($searchColumns, $search) {
                    foreach ($searchColumns as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        }

        // If no default sort field provided, use the model's primary key
        if ($defaultSortField === null) {
            $defaultSortField = (new $model)->getKeyName();
        }

        // Sort by requested field or default to primary key ascending for exports
        $allowedSorts = method_exists($model, 'getAllowedSorts') ? $model::getAllowedSorts() : [$defaultSortField];
        $sortBy = $request->input('sort_by', $defaultSortField);
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = $defaultSortField;
        }
        
        // For exports, default to ascending order unless explicitly requested otherwise
        $sortDir = $request->has('sort_dir') ? 
            (strtolower($request->input('sort_dir')) === 'desc' ? 'desc' : 'asc') : 
            'asc';
            
        return $query->orderBy($sortBy, $sortDir)->get();
    }

    /**
     * Return a CSV download response
     * @param string $filename Base filename for the CSV
     * @param callable $callback Function that writes CSV data
     * @param string|null $reportTitle Optional title for the report header
     */
    protected function downloadCsv(string $filename, callable $callback, ?string $reportTitle = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $this->getExportFilename(pathinfo($filename, PATHINFO_FILENAME), 'csv');
        
        if ($reportTitle) {
            // Wrap the original callback to include headers
            $originalCallback = $callback;
            $callback = function() use ($originalCallback, $reportTitle) {
                $file = fopen('php://output', 'w');
                // Write UTF-8 BOM so Excel on Windows recognizes UTF-8 encoding
                fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                $this->writeCsvHeader($file, $reportTitle);
                $originalCallback($file);
                fclose($file);
            };
        }
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}