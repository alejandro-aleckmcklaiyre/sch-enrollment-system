<?php

namespace App\Exports;

class DepartmentExport
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
                'ID' => $i->dept_id,
                'Code' => $i->dept_code,
                'Name' => $i->dept_name,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Code','Name'];
    }
}
