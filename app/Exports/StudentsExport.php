<?php

namespace App\Exports;

use App\Exports\Concerns\HasStandardExcelHeader;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentsExport implements FromCollection, WithEvents, WithHeadings
{
    use HasStandardExcelHeader;
    protected $filtered;
    protected $params;
    protected $sortField = 'student_id';

    public function __construct($filtered = false, $params = [])
    {
        $this->filtered = $filtered;
        $this->params = $params;
    }

    public function collection()
    {
        return new Collection($this->query());
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->applyStandardHeader($event, 'Students List', 17);
            }
        ];
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Program',
            'Last Name',
            'First Name',
            'Middle Name',
            'Date of Birth',
            'Place of Birth',
            'Sex',
            'Civil Status',
            'Nationality',
            'Religion',
            'Contact Number',
            'Email',
            'Address',
            'Guardian Name',
            'Guardian Contact Number',
            'Guardian Address',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    public function download($filename)
    {
        return Excel::download($this, $filename);
    }

    protected function query()
    {
        $query = Student::with('program');

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
            if (!empty($this->params['gender'])) {
                $query->where('gender', $this->params['gender']);
            }
            if (!empty($this->params['program_id'])) {
                $query->where('program_id', $this->params['program_id']);
            }
        }

        // Apply sorting with proper numeric handling for IDs
        $primaryKey = 'student_id';
        if (!empty($this->params['sort_by'])) {
            $sortBy = $this->params['sort_by'];
            $sortDir = strtolower($this->params['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            
            if ($sortBy === $primaryKey) {
                $query->orderByRaw("CAST(? AS UNSIGNED) ?", [$sortBy, $sortDir]);
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->orderBy($primaryKey, 'asc');
        }

        return $query->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'program' => optional($s->program)->program_name,
                'last_name' => $s->last_name,
                'first_name' => $s->first_name,
                'middle_name' => $s->middle_name,
                'birth_date' => $s->birth_date,
                'birth_place' => $s->birth_place,
                'sex' => $s->sex,
                'civil_status' => $s->civil_status,
                'nationality' => $s->nationality,
                'religion' => $s->religion,
                'contact_no' => $s->contact_no,
                'email' => $s->email,
                'address' => $s->address,
                'guardian_name' => $s->guardian_name,
                'guardian_contact_no' => $s->guardian_contact_no,
                'guardian_address' => $s->guardian_address,
                'status' => $s->status,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at
            ];
        })->toArray();
    }
}
