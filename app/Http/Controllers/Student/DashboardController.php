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
                'currentTerm' => null,
                'totalEnrolled' => 0,
                'needsProfileSetup' => true,
            ]);
        }

        // Get current enrollments
        $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('is_deleted', 0)
            ->with(['section.course', 'section.instructor', 'section.term'])
            ->get();

        // Get current term
        $currentTerm = \App\Models\Term::getCurrentTerm();

        // Get current term enrollments
        $currentEnrollments = $enrollments->filter(function ($enrollment) use ($currentTerm) {
            return $currentTerm && $enrollment->section && $enrollment->section->term_id == $currentTerm->term_id;
        });

        // Calculate GPA (simplified - you may need to adjust based on your grading system)
        $completedEnrollments = $enrollments->filter(function ($e) {
            return $e->status === 'COMPLETED' && $e->letter_grade;
        });

        return view('student.dashboard', [
            'student' => $student,
            'enrollments' => $enrollments,
            'currentEnrollments' => $currentEnrollments,
            'completedEnrollments' => $completedEnrollments,
            'currentTerm' => $currentTerm,
            'totalEnrolled' => $enrollments->where('status', 'ENROLLED')->count(),
            'needsProfileSetup' => false,
        ]);
    }
}
