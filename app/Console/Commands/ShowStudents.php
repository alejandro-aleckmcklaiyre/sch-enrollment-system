<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class ShowStudents extends Command
{
    protected $signature = 'app:show-students';
    protected $description = 'Show students with enrollments';

    public function handle()
    {
        $this->info("\n========== STUDENTS WITH ENROLLMENTS ==========\n");

        $students = Student::where('is_deleted', 0)
            ->get();

        foreach ($students->take(10) as $student) {
            $enrollmentCount = DB::table('tblenrollment')
                ->where('student_id', $student->student_id)
                ->where('is_deleted', 0)
                ->count();

            if ($enrollmentCount > 0) {
                $this->line("ID: {$student->student_id}, Name: {$student->first_name} {$student->last_name}, Email: {$student->email}, Enrollments: $enrollmentCount, User ID: {$student->user_id}");
            }
        }

        $this->info("\n================================================\n");
    }
}
