<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class TermExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;
    
    protected $items;
    
    public function __construct($items)
    {
        $this->items = $items;
    }
    
    public function collection(): Collection
    {
        return new Collection($this->items->map(function($term) {
            return [
                $term->term_id,
                $term->term_code,
                $term->start_date ? $term->start_date->format('Y-m-d') : '',
                $term->end_date ? $term->end_date->format('Y-m-d') : ''
            ];
        }));
    }

    public function headings(): array
    {
        return ['ID', 'Code', 'Start Date', 'End Date'];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->applyStandardHeader($event, 'Term Records', 4);
                $event->sheet->insertNewRowBefore(5, 1);
                
                // Set column widths
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('D')->setWidth(15);
            },
        ];
    }
}