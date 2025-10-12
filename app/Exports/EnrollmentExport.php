<?php

namespace App\Exports;

class EnrollmentExport
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function($i){
            return [
                'ID' => $i->enrollment_id,
                'Student' => optional($i->student)->student_no . ' - ' . optional($i->student)->last_name,
                'Section' => $i->section_id,
                'Date' => $i->date_enrolled,
                'Status' => $i->status,
                'Grade' => $i->letter_grade,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Student','Section','Date','Status','Grade'];
    }
}
