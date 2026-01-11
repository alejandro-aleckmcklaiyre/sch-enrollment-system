<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;

class CheckUserStudentLinks extends Command
{
    protected $signature = 'check:user-student-links';
    protected $description = 'Check user-student database links';

    public function handle()
    {
        // Get Aleck Alejandro user
        $aleckUser = User::where('name', 'Aleck Alejandro')->first();
        $this->info("Aleck User ID: " . ($aleckUser ? $aleckUser->id : 'Not found'));
        $this->info("Aleck User Email: " . ($aleckUser ? $aleckUser->email : 'Not found'));

        // Get Kevin Barcelos user
        $kevinUser = User::where('name', 'Kevin Joseph Barcelos')
            ->orWhere('name', 'Kevin Barcelos')
            ->first();
        $this->info("\nKevin User ID: " . ($kevinUser ? $kevinUser->id : 'Not found'));
        $this->info("Kevin User Email: " . ($kevinUser ? $kevinUser->email : 'Not found'));

        // Get student for Aleck
        if ($aleckUser) {
            $aleckStudent = $aleckUser->student;
            $this->info("\nAleck Student: " . ($aleckStudent ? $aleckStudent->first_name . ' ' . $aleckStudent->last_name : 'None'));
            $this->info("Aleck Student user_id: " . ($aleckStudent ? $aleckStudent->user_id : 'N/A'));
        }

        // Get student for Kevin
        if ($kevinUser) {
            $kevinStudent = $kevinUser->student;
            $this->info("\nKevin Student: " . ($kevinStudent ? $kevinStudent->first_name . ' ' . $kevinStudent->last_name : 'None'));
            $this->info("Kevin Student user_id: " . ($kevinStudent ? $kevinStudent->user_id : 'N/A'));
        }

        // Show all students and their user_ids
        $this->info("\n\nAll students with user_ids:");
        $allStudents = Student::select('student_id', 'first_name', 'last_name', 'user_id')->get();
        foreach ($allStudents as $s) {
            $this->info("Student: " . $s->first_name . " " . $s->last_name . " (ID: " . $s->student_id . ") - user_id: " . $s->user_id);
        }

        $this->info("\n\nAll student users:");
        $allUsers = User::where('role', 'student')->select('id', 'name', 'email', 'role')->get();
        foreach ($allUsers as $u) {
            $this->info("User: " . $u->name . " (ID: " . $u->id . ") - Email: " . $u->email);
        }
    }
}
