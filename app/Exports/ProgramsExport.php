<?php

namespace App\Exports;

use App\Models\Program;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class ProgramsExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;

    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return ['ID','Program Code','Program Name','Dept ID'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $this->applyStandardHeader($event, 'Program Records', 4);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
