<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FixUserStudentLinks extends Command
{
    protected $signature = 'fix:user-student-links';
    protected $description = 'Fix incorrect user-student associations';

    public function handle()
    {
        $this->info("Fixing user-student associations...\n");

        // Since Kevin (Student 4) doesn't have a user account, set user_id to NULL
        DB::table('tblstudent')->where('student_id', 4)->update(['user_id' => null]);
        $this->info("✓ Student 4 (Kevin Joseph Barcelos) - set user_id to NULL");

        // Link Student 1 to User 3
        DB::table('tblstudent')->where('student_id', 1)->update(['user_id' => 3]);
        $this->info("✓ Student 1 (Aleck McKlaiyre Alejandro) - set user_id to 3");

        // For User 5 students, set to NULL
        DB::table('tblstudent')->where('user_id', 5)->update(['user_id' => null]);
        $this->info("✓ Students with user_id 5 - set to NULL");

        $this->info("\n✅ User-student associations fixed!");
        $this->info("\nNew associations:");
        
        $students = DB::table('tblstudent')
            ->whereNotNull('user_id')
            ->select('student_id', 'first_name', 'last_name', 'user_id')
            ->orderBy('user_id')
            ->get();
        
        foreach ($students as $s) {
            $this->info("Student: " . $s->first_name . " " . $s->last_name . " (ID: " . $s->student_id . ") -> user_id: " . $s->user_id);
        }
    }
}

