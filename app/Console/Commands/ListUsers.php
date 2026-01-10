<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;

class ListUsers extends Command
{
    protected $signature = 'app:list-users';
    protected $description = 'List available users and students';

    public function handle()
    {
        $this->info("\n========== USERS & STUDENTS ==========\n");

        $users = User::all();
        foreach ($users as $user) {
            $student = Student::where('student_id', $user->student_id ?? null)->first();
            $enrollments = $student ? Enrollment::where('student_id', $student->student_id)->where('is_deleted', 0)->count() : 0;
            
            $this->line("ID: {$user->id}, Email: {$user->email}, Role: {$user->role}, Student: {$user->student_id}, Enrollments: $enrollments");
        }

        $this->line("\n--- Students with Enrollments ---");
        $studentsWithEnrollments = Student::where('is_deleted', 0)
            ->has('enrollments')
            ->with('enrollments')
            ->limit(10)
            ->get();

        foreach ($studentsWithEnrollments as $student) {
            $enrollmentCount = Enrollment::where('student_id', $student->student_id)->where('is_deleted', 0)->count();
            $this->line("Student {$student->student_id} ({$student->first_name} {$student->last_name}): {$enrollmentCount} enrollments");
        }

        $this->info("\n======================================\n");
    }
}
