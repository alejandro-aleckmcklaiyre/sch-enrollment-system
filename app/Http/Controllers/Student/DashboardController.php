<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display student dashboard with enrollments overview
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        // If student record doesn't exist, show a welcome message
        if (!$student) {
            return view('student.dashboard', [
                'student' => null,
                'enrollments' => collect(),
                'currentEnrollments' => collect(),
                'completedEnrollments' => collect(),
                'announcements' => collect(),
                'currentTerm' => null,
                'totalEnrolled' => 0,
                'totalCreditsCompleted' => 0,
                'totalCreditsEnrolled' => 0,
                'needsProfileSetup' => true,
            ]);
        }

        // Get current enrollments
        $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('is_deleted', 0)
            ->with(['section.course', 'section.instructor', 'section.term', 'section.room'])
            ->get();

        // Get current term
        $currentTerm = \App\Models\Term::getCurrentTerm();

        // Get current term enrollments
        $currentEnrollments = $enrollments->filter(function ($enrollment) use ($currentTerm) {
            return $currentTerm && $enrollment->section && $enrollment->section->term_id == $currentTerm->term_id && $enrollment->status === 'ENROLLED';
        });

        // Calculate completed enrollments
        $completedEnrollments = $enrollments->filter(function ($e) {
            return $e->status === 'COMPLETED' && $e->letter_grade;
        });

        // Calculate total credits
        $totalCreditsCompleted = $completedEnrollments->sum(function ($e) {
            return $e->section?->course?->units ?? 0;
        });

        $totalCreditsEnrolled = $currentEnrollments->sum(function ($e) {
            return $e->section?->course?->units ?? 0;
        });

        // Get mock announcements
        $announcements = collect([
            [
                'id' => 1,
                'title' => 'Welcome to Student Portal',
                'body' => 'Welcome to the enrollment system. You can view your courses, schedule, and academic progress here.',
                'date' => now()->subDays(2),
                'type' => 'system'
            ],
            [
                'id' => 2,
                'title' => 'Enrollment Period Open',
                'body' => 'The enrollment period for the next term is now open. You can browse courses in the Course Catalog and enroll in available sections.',
                'date' => now()->subDays(1),
                'type' => 'academic'
            ],
        ])->take(3);

        return view('student.dashboard', [
            'student' => $student,
            'enrollments' => $enrollments,
            'currentEnrollments' => $currentEnrollments,
            'completedEnrollments' => $completedEnrollments,
            'announcements' => $announcements,
            'currentTerm' => $currentTerm,
            'totalEnrolled' => $enrollments->where('status', 'ENROLLED')->count(),
            'totalCreditsCompleted' => $totalCreditsCompleted,
            'totalCreditsEnrolled' => $totalCreditsEnrolled,
            'needsProfileSetup' => false,
        ]);
    }
}
