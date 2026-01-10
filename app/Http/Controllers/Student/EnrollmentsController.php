<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EnrollmentsController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('is_deleted', 0)
            ->with(['section.course', 'section.instructor', 'section.term', 'section.room'])
            ->orderBy('date_enrolled', 'desc')
            ->get();

        $currentTerm = \App\Models\Term::getCurrentTerm();

        return view('student.enrollments.index', [
            'enrollments' => $enrollments,
            'currentTerm' => $currentTerm,
            'student' => $student
        ]);
    }
}
