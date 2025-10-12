<?php

namespace App\Exports;

class SectionExport
{
    protected $items;
    public function __construct($items){ $this->items = $items; }
    public function collection(){
        return $this->items->map(function($i){
            return [
                'ID'=>$i->section_id,
                'Code'=>$i->section_code,
                'Course'=>optional($i->course)->course_code,
                'Term'=>optional($i->term)->term_code,
                'Instructor'=>optional($i->instructor)->last_name,
                'Room'=>optional($i->room)->room_code,
            ];
        });
    }
    public function headings(): array{ return ['ID','Code','Course','Term','Instructor','Room']; }
}
