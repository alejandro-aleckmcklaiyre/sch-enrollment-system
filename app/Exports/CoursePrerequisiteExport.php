<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class CoursePrerequisiteExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;
    protected $items; public function __construct($items){ $this->items = $items; }
    public function collection(){
        return $this->items->map(function($i){
            return [
                optional($i->course)->course_code,
                optional($i->prereq)->course_code,
            ];
        });
    }
    public function headings(): array{ return ['Course','Prerequisite']; }
    public function registerEvents(): array{
        return [
            AfterSheet::class => function(AfterSheet $event){
                $this->applyStandardHeader($event, 'Course Prerequisites', 2);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
