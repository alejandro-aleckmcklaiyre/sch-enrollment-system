<?php

namespace App\Models\Traits;

use Illuminate\Support\Collection;

trait ExportTrait
{
    protected $filtered = false;
    protected $params = [];

    public function __construct($filtered = false, $params = [])
    {
        $this->filtered = $filtered;
        $this->params = $params;
    }

    protected function getDefaultSortField()
    {
        return $this->sortField ?? 'id';
    }

    protected function applySorting($query)
    {
        if (!empty($this->params['sort_by'])) {
            $sortBy = $this->params['sort_by'];
            $sortDir = strtolower($this->params['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            return $query->orderBy($sortBy, $sortDir);
        }
        return $query->orderBy($this->getDefaultSortField(), 'asc');
    }

    public function getExcelStyles()
    {
        return [
            // Title row style
            'B1:I1' => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center']
            ],
            // Date row style
            'B2:I2' => [
                'font' => ['size' => 10],
                'alignment' => ['horizontal' => 'center']
            ]
        ];
    }

    public function beforeExport($sheet)
    {
        // Insert rows for header
        $sheet->insertNewRowBefore(1, 3);
        
        // Set header content
        $sheet->setCellValue('B1', 'Polytechnic University of the Philippines – Taguig Campus');
        $sheet->setCellValue('B2', 'Date created: ' . date('F j, Y'));

        // Merge cells for header
        $sheet->mergeCells('B1:I1');
        $sheet->mergeCells('B2:I2');

        // Apply styles
        foreach ($this->getExcelStyles() as $range => $style) {
            $sheet->getStyle($range)->applyFromArray($style);
        }

        // Try to insert logo if available
        try {
            if (file_exists(public_path('images/pup_logo.jpg'))) {
                $sheet->insertImage(
                    public_path('images/pup_logo.jpg'),
                    'A1',
                    ['height' => 60]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Could not insert logo in Excel export: ' . $e->getMessage());
        }

        // Set print footer
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);
        $sheet->getHeaderFooter()
            ->setOddFooter('Page &P of &N — Printed on: ' . date('F j, Y'));
    }
}