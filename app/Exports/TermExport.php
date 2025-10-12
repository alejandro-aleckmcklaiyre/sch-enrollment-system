<?php

namespace App\Exports;

class TermExport
{
    protected $items; public function __construct($items){ $this->items = $items; }
    public function collection(){ return $this->items->map(fn($i)=>[$i->term_id,$i->term_code,$i->start_date,$i->end_date]); }
    public function headings(): array{ return ['ID','Code','Start','End']; }
}
