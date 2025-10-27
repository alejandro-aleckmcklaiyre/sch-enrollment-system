<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class TermExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;
    protected $items;
    public function __construct($items){ $this->items = $items; }
    public function collection(){ return $this->items->map(fn($i)=>[$i->term_id,$i->term_code,$i->start_date,$i->end_date]); }
    public function headings(): array{ return ['ID','Code','Start','End']; }
    public function registerEvents(): array{
        return [
            AfterSheet::class => function(AfterSheet $event){
                $this->applyStandardHeader($event, 'Term Records', 4);
                $event->sheet->insertNewRowBefore(5, 1);
            },
        ];
    }
}
