<?php

namespace App\Exports;

use App\Models\Instructor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class InstructorExport implements FromCollection, WithHeadings, WithEvents
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
                $i->instructor_id,
                $i->last_name,
                $i->first_name,
                $i->email,
                $i->dept_id,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Last Name','First Name','Email','Dept ID'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->applyStandardHeader($event, 'Instructor Records', 5);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
