<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class SectionExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;
    protected $items;
    public function __construct($items){ $this->items = $items; }
    public function collection(){
        return $this->items->map(function($i){
            return [
                $i->section_id,
                $i->section_code,
                optional($i->course)->course_code,
                optional($i->term)->term_code,
                optional($i->instructor)->last_name,
                optional($i->room)->room_code,
            ];
        });
    }
    public function headings(): array{ return ['ID','Code','Course','Term','Instructor','Room']; }
    public function registerEvents(): array{
        return [
            AfterSheet::class => function(AfterSheet $event){
                $this->applyStandardHeader($event, 'Section Records', 6);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
