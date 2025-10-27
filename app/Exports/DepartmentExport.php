<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class DepartmentExport implements FromCollection, WithHeadings, WithEvents
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
                $i->dept_id,
                $i->dept_code,
                $i->dept_name,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Code','Name'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->applyStandardHeader($event, 'Department Records', 3);
                // move table start to row 5
                $event->sheet->insertNewRowBefore(5, 1);
            },
        ];
    }
}
