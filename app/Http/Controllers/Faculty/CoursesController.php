<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        $courses = collect();

        if ($instructor) {
            $courses = \App\Models\Course::whereIn(
                'course_id',
                \App\Models\Section::where('instructor_id', $instructor->instructor_id)
                    ->pluck('course_id')
                    ->toArray()
            )
            ->where('is_deleted', 0)
            ->distinct()
            ->get();
        }

        return view('faculty.courses', ['courses' => $courses]);
    }
}
