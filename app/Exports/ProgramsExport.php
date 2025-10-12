<?php

namespace App\Exports;

use App\Models\Program;

class ProgramsExport
{
    public function collection()
    {
        return Program::select(['program_id','program_code','program_name','dept_id'])->orderBy('program_id','desc')->get();
    }

    public function headings(): array
    {
        return ['ID','Program Code','Program Name','Dept ID'];
    }
}
