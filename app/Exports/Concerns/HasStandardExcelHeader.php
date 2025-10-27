<?php

namespace App\Exports\Concerns;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

trait HasStandardExcelHeader
{
    /**
     * Register AfterSheet modifications to insert a standard header
     * Intended for use in exports that implement WithEvents
     */
    public function applyStandardHeader(AfterSheet $event, string $reportTitle, int $tableStartColumnCount = 7)
    {
        $sheet = $event->sheet->getDelegate();

        // Define cell range to merge based on a guessed column width
        $lastCol = chr(64 + max(5, $tableStartColumnCount)); // simple fallback for A..Z

        // Add logo
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $logoPath = public_path('images/pup_logo.jpg');
        if (file_exists($logoPath)) {
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(36);
            $drawing->setWorksheet($sheet);
        }

        // Merge header cells
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Set header values with institutional branding
        $sheet->setCellValue('A1', 'POLYTECHNIC UNIVERSITY OF THE PHILIPPINES – TAGUIG CAMPUS');
        $sheet->setCellValue('A2', 'Generated on: ' . now()->format(config('export.date_format')));
        $sheet->setCellValue('A3', strtoupper($reportTitle));

        // Center and style headers
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);

        // Add spacing for logo and ensure clean layout
        $sheet->getRowDimension(1)->setRowHeight(38); // Taller for logo
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(16);
        $sheet->getRowDimension(4)->setRowHeight(8);

        // Auto-fit columns for better readability
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Add footer row below existing data
        try {
            $highestRow = (int) $sheet->getHighestRow();
            $footerRow = $highestRow + 2; // leave one blank row
            $lastCol = chr(64 + max(5, $tableStartColumnCount));
            $sheet->mergeCells("A{$footerRow}:{$lastCol}{$footerRow}");
            $sheet->setCellValue("A{$footerRow}", 'Printed on: ' . now()->format(config('export.date_format')));
            $sheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$footerRow}")->getFont()->setBold(true);
        } catch (\Throwable $ex) {
            // non-fatal: ignore footer if any issue occurs
        }
    }
}
