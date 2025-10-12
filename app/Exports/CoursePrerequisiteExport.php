<?php

namespace App\Exports;

class CoursePrerequisiteExport
{
    protected $items; public function __construct($items){ $this->items = $items; }
    public function collection(){
        return $this->items->map(function($i){
            return [
                'Course'=>optional($i->course)->course_code,
                'Prerequisite'=>optional($i->prereq)->course_code,
            ];
        });
    }
    public function headings(): array{ return ['Course','Prerequisite']; }
}
