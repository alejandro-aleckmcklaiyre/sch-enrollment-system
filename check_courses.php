<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== COURSE CHECK ===\n\n";

$coursesCount = Course::count();
echo "Total courses: {$coursesCount}\n\n";

echo "Sample courses:\n";
$courses = Course::limit(5)->get();
foreach($courses as $course) {
    echo "- ID: {$course->course_id}, Code: {$course->course_code}, Title: {$course->course_title}\n";
}

echo "\n=== SECTION COURSE_ID CHECK ===\n";
$sectionsWithInvalidCourses = DB::table('tblsection')
    ->leftJoin('tblcourse', 'tblsection.course_id', '=', 'tblcourse.course_id')
    ->where('tblsection.is_deleted', 0)
    ->where(function($q) {
        $q->whereNull('tblcourse.course_id')
          ->orWhere('tblsection.course_id', 0);
    })
    ->select('tblsection.section_id', 'tblsection.course_id', 'tblcourse.course_title')
    ->get();

echo "Sections with invalid course_id: " . $sectionsWithInvalidCourses->count() . "\n";
foreach($sectionsWithInvalidCourses as $section) {
    echo "- Section {$section->section_id}: course_id = {$section->course_id}, course_title = " . ($section->course_title ?? 'NULL') . "\n";
}

echo "\n=== STUDENT'S ENROLLMENT SECTIONS ===\n";
$studentSections = DB::table('tblenrollment')
    ->join('tblstudent', 'tblenrollment.student_id', '=', 'tblstudent.student_id')
    ->join('users', 'tblstudent.user_id', '=', 'users.id')
    ->where('users.email', 'student@test.com')
    ->where('tblenrollment.is_deleted', 0)
    ->select('tblenrollment.enrollment_id', 'tblenrollment.section_id', 'tblsection.course_id', 'tblcourse.course_title')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->leftJoin('tblcourse', 'tblsection.course_id', '=', 'tblcourse.course_id')
    ->get();

echo "Student's enrollment sections:\n";
foreach($studentSections as $enrollment) {
    echo "- Enrollment {$enrollment->enrollment_id}: Section {$enrollment->section_id}, Course ID {$enrollment->course_id}, Title: " . ($enrollment->course_title ?? 'NULL') . "\n";
}

echo "\n=== DONE ===\n";