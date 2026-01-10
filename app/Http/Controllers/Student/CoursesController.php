<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;

class CoursesController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        $currentTerm = \App\Models\Term::getCurrentTerm();

        $courses = Course::where('is_deleted', 0)
            ->with('department')
            ->paginate(15);

        // Get already enrolled courses
        $enrolledCourseIds = \App\Models\Enrollment::withoutGlobalScope('is_deleted')
            ->where('student_id', $student->student_id)
            ->join('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
            ->where('tblenrollment.is_deleted', 0)
            ->where('tblsection.is_deleted', 0)
            ->pluck('tblsection.course_id')
            ->toArray();

        return view('student.courses.index', [
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
            'currentTerm' => $currentTerm,
            'student' => $student
        ]);
    }

    public function show(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        $course->load(['department']);
        
        $currentTerm = \App\Models\Term::getCurrentTerm();
        
        $sections = \App\Models\Section::where('course_id', $course->course_id)
            ->with(['term', 'instructor', 'room'])
            ->when($currentTerm, function($query) use ($currentTerm) {
                return $query->where('term_id', $currentTerm->term_id);
            })
            ->get();

        // Check if already enrolled in this course
        $isEnrolled = \App\Models\Enrollment::withoutGlobalScope('is_deleted')
            ->where('student_id', $student->student_id)
            ->join('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
            ->where('tblsection.course_id', $course->course_id)
            ->where('tblenrollment.is_deleted', 0)
            ->where('tblsection.is_deleted', 0)
            ->exists();

        return view('student.courses.show', [
            'course' => $course,
            'sections' => $sections,
            'isEnrolled' => $isEnrolled,
            'student' => $student
        ]);
    }

    public function enroll(Request $request, Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect('/student/dashboard')->with('error', 'Student profile not found');
        }

        // Validate that a section was selected
        $validated = $request->validate([
            'section_id' => 'required|exists:tblsection,section_id'
        ]);

        $sectionId = $validated['section_id'];
        
        // Check if student is already enrolled in this section
        $existingEnrollment = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->where('section_id', $sectionId)
            ->where('is_deleted', 0)
            ->first();

        if ($existingEnrollment) {
            return redirect('/student/courses/' . $course->course_id)
                ->with('error', 'You are already enrolled in this section');
        }

        // Check section capacity
        $section = \App\Models\Section::find($sectionId);
        if (!$section) {
            return redirect('/student/courses/' . $course->course_id)
                ->with('error', 'Section not found');
        }

        $enrollmentCount = \App\Models\Enrollment::where('section_id', $sectionId)
            ->where('status', 'ENROLLED')
            ->where('is_deleted', 0)
            ->count();

        if ($enrollmentCount >= $section->max_capacity) {
            return redirect('/student/courses/' . $course->course_id)
                ->with('error', 'This section is already at full capacity');
        }

        // Create enrollment record
        try {
            $enrollment = new \App\Models\Enrollment();
            $enrollment->student_id = $student->student_id;
            $enrollment->section_id = $sectionId;
            $enrollment->course_id = $course->course_id;
            $enrollment->date_enrolled = now();
            $enrollment->status = 'ENROLLED';
            $enrollment->save();

            \Log::info('Student ' . $student->student_id . ' enrolled in section ' . $sectionId);

            return redirect('/student/enrollments')
                ->with('success', 'Successfully enrolled in ' . $course->course_title);
        } catch (\Exception $e) {
            \Log::error('Enrollment error: ' . $e->getMessage());
            return redirect('/student/courses/' . $course->course_id)
                ->with('error', 'An error occurred while enrolling. Please try again.');
        }
    }
}
