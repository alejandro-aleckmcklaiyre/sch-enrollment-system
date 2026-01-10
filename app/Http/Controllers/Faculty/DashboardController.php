<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display faculty dashboard with assigned sections overview
     */
    public function index()
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        
        // If instructor record doesn't exist, show a welcome message
        if (!$instructor) {
            return view('faculty.dashboard', [
                'instructor' => null,
                'sections' => collect(),
                'sectionsWithStats' => collect(),
                'totalStudents' => 0,
                'totalSections' => 0,
                'totalEnrollments' => 0,
                'recentEnrollments' => collect(),
                'needsProfileSetup' => true,
            ]);
        }

        // Get sections assigned to this instructor
        $sections = Section::where('instructor_id', $instructor->instructor_id)
            ->with(['course', 'term', 'room', 'enrollments.student'])
            ->orderBy('section_code')
            ->get();

        // Get total students (unique) across all sections
        $totalStudents = Enrollment::whereIn(
            'section_id',
            $sections->pluck('section_id')->toArray()
        )
        ->where('status', '!=', 'DROPPED')
        ->where('is_deleted', 0)
        ->distinct('student_id')
        ->count();

        // Get total enrollments
        $totalEnrollments = Enrollment::whereIn(
            'section_id',
            $sections->pluck('section_id')->toArray()
        )
        ->where('is_deleted', 0)
        ->count();

        // Build sections with statistics
        $sectionsWithStats = $sections->map(function($section) {
            $enrolledCount = $section->enrollments->where('status', '!=', 'DROPPED')->count();
            $droppedCount = $section->enrollments->where('status', 'DROPPED')->count();
            
            return [
                'section_id' => $section->section_id,
                'section_code' => $section->section_code,
                'course_code' => $section->course->course_code ?? 'N/A',
                'course_title' => $section->course->course_title ?? 'N/A',
                'term' => $section->term->term_name ?? 'N/A',
                'room' => $section->room->room_code ?? 'TBA',
                'enrolled_count' => $enrolledCount,
                'dropped_count' => $droppedCount,
                'total_count' => $section->enrollments->count(),
                'schedule_days' => $section->schedule_days ?? 'TBD',
                'schedule_time' => $section->schedule_time ?? 'TBD',
            ];
        });

        // Get recent enrollments for activity
        $recentEnrollments = Enrollment::whereIn('section_id', $sections->pluck('section_id'))
            ->with(['student', 'section.course'])
            ->where('is_deleted', 0)
            ->orderBy('date_enrolled', 'desc')
            ->take(5)
            ->get();

        return view('faculty.dashboard', [
            'instructor' => $instructor,
            'user' => $user,
            'sections' => $sections,
            'sectionsWithStats' => $sectionsWithStats,
            'totalStudents' => $totalStudents,
            'totalSections' => $sections->count(),
            'totalEnrollments' => $totalEnrollments,
            'recentEnrollments' => $recentEnrollments,
            'needsProfileSetup' => false,
        ]);
    }
}
