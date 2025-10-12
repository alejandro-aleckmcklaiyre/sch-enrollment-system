<?php

namespace App\Exports;

use App\Models\Student;

class StudentsExport
{
    protected $filtered;
    protected $params;

    public function __construct($filtered = false, $params = [])
    {
        $this->filtered = $filtered;
        $this->params = $params;
    }

    public function collection()
    {
        $query = Student::query();

        if ($this->filtered) {
            if (!empty($this->params['search'])) {
                $search = $this->params['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('student_no', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if (!empty($this->params['year_level'])) {
                $query->where('year_level', $this->params['year_level']);
            }
        }

        return $query->orderBy('student_id', 'desc')->get(['student_no','last_name','first_name','middle_name','email','gender','birthdate','year_level','program_id']);
    }

    public function headings(): array
    {
        return ['Student No','Last Name','First Name','Middle Name','Email','Gender','Birthdate','Year Level','Program'];
    }
}
