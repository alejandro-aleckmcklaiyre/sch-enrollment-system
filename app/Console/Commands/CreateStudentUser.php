<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;

class CreateStudentUser extends Command
{
    protected $signature = 'app:create-student-user {--email=student@test.com} {--password=password} {--student-id=1}';
    protected $description = 'Create a student user for testing';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $studentId = $this->option('student-id');

        // Check if student exists
        $student = Student::where('student_id', $studentId)->where('is_deleted', 0)->first();
        if (!$student) {
            $this->error("Student with ID $studentId not found");
            return;
        }

        // Check if user already exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => "{$student->first_name} {$student->last_name}",
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'student',
            ]);
            $this->info("Created user with email: $email");
        } else {
            $this->info("User with email $email already exists, skipping creation");
        }

        // Link student to user
        $student->user_id = $user->id;
        $student->save();

        $this->info("✓ Student {$student->student_id} ({$student->first_name} {$student->last_name}) linked to user $email");
        $this->info("✓ Password: $password");
        
        // Show enrollment count
        $enrollmentCount = DB::table('tblenrollment')
            ->where('student_id', $student->student_id)
            ->where('is_deleted', 0)
            ->count();
        
        $this->info("✓ Student has $enrollmentCount enrollments");
    }
}
