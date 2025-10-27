<?php

namespace App\Exports;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Concerns\HasStandardExcelHeader;

class RoomExport implements FromCollection, WithHeadings, WithEvents
{
    use HasStandardExcelHeader;

    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function($r){
            return [
                $r->room_id,
                $r->building,
                $r->room_code,
                $r->capacity,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Building','Room Code','Capacity'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->applyStandardHeader($event, 'Room Records', 4);
                $event->sheet->insertNewRowBefore(5, 1);
            }
        ];
    }
}
