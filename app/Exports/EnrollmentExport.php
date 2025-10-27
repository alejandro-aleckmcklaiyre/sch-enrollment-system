<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class EnrollmentExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function($i){
            return [
                $i->enrollment_id,
                optional($i->student)->student_no . ' - ' . optional($i->student)->last_name,
                $i->section_id,
                $i->date_enrolled,
                $i->status,
                $i->letter_grade,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Student','Section','Date','Status','Grade'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $this->applyStandardHeader($event, 'Enrollment Records', 6);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
