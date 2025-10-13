<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StudentsExport implements FromCollection, WithHeadings, WithDrawings, WithEvents
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

        // enforce ascending order for exports
        $students = $query->orderBy($this->params['sort_by'] ?? 'student_id', 'asc')
            ->get();

        return $students->map(function ($s) {
            return [
                'student_no' => $s->student_no,
                'last_name' => $s->last_name,
                'first_name' => $s->first_name,
                'middle_name' => $s->middle_name,
                'email' => $s->email,
                'gender' => $s->gender,
                'birthdate' => $s->birthdate,
                'year_level' => $s->year_level,
                'program' => optional($s->program)->program_name,
            ];
        });
    }

    public function headings(): array
    {
        return ['Student No','Last Name','First Name','Middle Name','Email','Gender','Birthdate','Year Level','Program'];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('PUP Logo');
        $drawing->setPath(public_path('images/pup_logo.jpg'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function registerEvents(): array
    {
        $title = 'Polytechnic University of the Philippines – Taguig Campus';
        $date = date('F j, Y');

        return [
            AfterSheet::class => function(AfterSheet $event) use ($title, $date) {
                $sheet = $event->sheet->getDelegate();
                // shift existing rows down to make room for header (logo + text)
                $sheet->insertNewRowBefore(1, 3);
                // set header text
                // place title centered across columns B to I
                $sheet->setCellValue('B1', $title);
                $sheet->setCellValue('B2', 'Date created: ' . $date);
                // style header
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B2')->getFont()->setSize(10);
                // Merge header area across a few columns for nicer layout
                $sheet->mergeCells('B1:I1');
                $sheet->mergeCells('B2:I2');
                // center the merged header
                $sheet->getStyle('B1:I2')->getAlignment()->setHorizontal("center");
                // Footer for printing (page numbers + print date)
                $sheet->getHeaderFooter()->setOddFooter('Page &P of &N — Printed on: ' . date('F j, Y'));
            }
        ];
    }
}
