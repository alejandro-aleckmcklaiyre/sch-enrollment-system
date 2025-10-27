<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Exports\Concerns\HasStandardExcelHeader;

class CoursesExport implements FromCollection, WithHeadings, WithDrawings, WithEvents
{
    protected $items;
    use HasStandardExcelHeader;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function($i){
            return [
                $i->course_id,
                $i->course_code,
                $i->course_title,
                $i->units,
                $i->lecture_hours,
                $i->lab_hours,
                optional($i->department)->dept_name ?? $i->dept_id,
            ];
        });
    }

    public function headings(): array
    {
        // Headings will be placed starting at row 5 by the exporter
        return [
            'ID',
            'Course Code', 
            'Course Title',
            'Units',
            'Lecture Hours',
            'Lab Hours',
            'Department'
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('PUP Logo');
        $drawing->setPath(public_path('images/pup_logo.jpg'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');

        return [$drawing];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Apply standard header
                $this->applyStandardHeader($event, 'Course Records', 7);

                // Move table headers and data down so table starts at row 5
                $event->sheet->insertNewRowBefore(5, 1);
                $event->sheet->insertNewRowBefore(5, 1);
            },
        ];
    }
}
