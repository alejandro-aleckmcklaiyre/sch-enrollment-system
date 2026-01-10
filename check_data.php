<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n========== DATABASE DATA CHECK ==========\n\n";

echo "Courses: " . DB::table('tblcourse')->where('is_deleted', 0)->count() . "\n";
echo "Sections: " . DB::table('tblsection')->where('is_deleted', 0)->count() . "\n";
echo "Terms: " . DB::table('tblterm')->where('is_deleted', 0)->count() . "\n";
echo "Students: " . DB::table('tblstudent')->where('is_deleted', 0)->count() . "\n";
echo "Instructors: " . DB::table('tblinstructor')->where('is_deleted', 0)->count() . "\n";
echo "Enrollments: " . DB::table('tblenrollment')->where('is_deleted', 0)->count() . "\n";
echo "Rooms: " . DB::table('tblroom')->where('is_deleted', 0)->count() . "\n";

echo "\n--- Current Term ---\n";
$currentDate = date('Y-m-d');
$term = DB::table('tblterm')
    ->where('is_deleted', 0)
    ->where('start_date', '<=', $currentDate)
    ->where('end_date', '>=', $currentDate)
    ->first();
if ($term) {
    echo "Current: {$term->term_code} - {$term->term_name}\n";
} else {
    echo "No current term set\n";
}

echo "\n--- Student Enrollment Relationships ---\n";
$student = DB::table('tblstudent')->where('is_deleted', 0)->first();
if ($student) {
    echo "Checking student: {$student->first_name} {$student->last_name} (ID: {$student->student_id})\n";

    $enrollments = DB::table('tblenrollment')
        ->where('student_id', $student->student_id)
        ->where('is_deleted', 0)
        ->get();

    echo "Enrollments found: " . $enrollments->count() . "\n";

    foreach ($enrollments as $enrollment) {
        echo "\nEnrollment ID: {$enrollment->enrollment_id}, Status: {$enrollment->status}\n";

        // Check section
        $section = DB::table('tblsection')->where('section_id', $enrollment->section_id)->first();
        if ($section) {
            echo "  Section: {$section->section_code}\n";

            // Check course
            $course = DB::table('tblcourse')->where('course_id', $section->course_id)->first();
            echo "  Course: " . ($course ? $course->course_title : 'NULL') . "\n";

            // Check instructor
            $instructor = DB::table('tblinstructor')->where('instructor_id', $section->instructor_id)->first();
            echo "  Instructor: " . ($instructor ? $instructor->first_name . ' ' . $instructor->last_name : 'NULL') . "\n";

            // Check room
            $room = DB::table('tblroom')->where('room_id', $section->room_id)->first();
            echo "  Room: " . ($room ? $room->room_code : 'NULL') . "\n";

            // Check term
            $term = DB::table('tblterm')->where('term_id', $section->term_id)->first();
            echo "  Term: " . ($term ? $term->term_name : 'NULL') . "\n";
        } else {
            echo "  Section: NULL\n";
        }
    }
} else {
    echo "No students found!\n";
}

echo "\n==========================================\n";
