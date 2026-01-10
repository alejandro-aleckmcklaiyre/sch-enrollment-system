<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;

class CheckCurrentUser extends Command
{
    protected $signature = 'app:check-current-user';
    protected $description = 'Check current authenticated user and their student record';

    public function handle()
    {
        $this->info("\n========== CURRENT USER CHECK ==========\n");

        // Check if there's an authenticated user in session
        if (Auth::check()) {
            $user = Auth::user();
            $this->line("✓ Authenticated User:");
            $this->line("  ID: {$user->id}");
            $this->line("  Name: {$user->name}");
            $this->line("  Email: {$user->email}");
            $this->line("  Role: {$user->role}");

            // Check if user has student relationship
            $student = $user->student;
            if ($student) {
                $this->line("✓ Student Record Found:");
                $this->line("  Student ID: {$student->student_id}");
                $this->line("  Name: {$student->first_name} {$student->last_name}");
                $this->line("  Email: {$student->email}");

                // Check enrollments
                $enrollmentCount = $student->enrollments()->where('is_deleted', 0)->count();
                $this->line("  Enrollments: {$enrollmentCount}");
            } else {
                $this->error("✗ No Student Record Found!");
                $this->line("  This is why you're being redirected to dashboard");
            }
        } else {
            $this->error("✗ No authenticated user!");
        }

        $this->info("\n========================================\n");
    }
}
