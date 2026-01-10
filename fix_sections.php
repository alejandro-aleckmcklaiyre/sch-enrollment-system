<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Creating additional sections...\n";

// Get some courses, terms, instructors, and rooms
$courses = DB::table('tblcourse')->where('is_deleted', 0)->limit(10)->get();
$terms = DB::table('tblterm')->where('is_deleted', 0)->get();
$instructors = DB::table('tblinstructor')->where('is_deleted', 0)->get();
$rooms = DB::table('tblroom')->where('is_deleted', 0)->get();

// Create sections for each course-term combination
$sectionId = 3; // Start from 3 since 1 and 2 already exist
foreach($courses as $course) {
    foreach($terms as $term) {
        // Create a section for this course-term combo
        DB::table('tblsection')->insert([
            'section_id' => $sectionId,
            'section_code' => 'SEC' . $sectionId,
            'course_id' => $course->course_id,
            'term_id' => $term->term_id,
            'instructor_id' => $instructors->random()->instructor_id,
            'room_id' => $rooms->random()->room_id,
            'max_capacity' => rand(20, 40),
            'is_deleted' => 0
        ]);

        echo "Created section ID {$sectionId}: Course {$course->course_code}, Term {$term->term_code}\n";
        $sectionId++;

        if ($sectionId > 50) break 2; // Limit to reasonable number
    }
}

echo "\nUpdating orphaned enrollments...\n";

// Update enrollments that reference non-existent sections
// But avoid unique constraint violations
$validSections = DB::table('tblsection')->where('is_deleted', 0)->pluck('section_id')->toArray();

if (!empty($validSections)) {
    $orphanedEnrollments = DB::table('tblenrollment')
        ->where('is_deleted', 0)
        ->whereNotIn('section_id', $validSections)
        ->get();

    foreach($orphanedEnrollments as $enrollment) {
        // Find a section this student is not already enrolled in
        $existingSections = DB::table('tblenrollment')
            ->where('student_id', $enrollment->student_id)
            ->where('is_deleted', 0)
            ->pluck('section_id')
            ->toArray();

        $availableSections = array_diff($validSections, $existingSections);

        if (!empty($availableSections)) {
            $randomSectionId = $availableSections[array_rand($availableSections)];
            DB::table('tblenrollment')
                ->where('enrollment_id', $enrollment->enrollment_id)
                ->update(['section_id' => $randomSectionId]);

            echo "Updated enrollment {$enrollment->enrollment_id} to use section {$randomSectionId}\n";
        } else {
            echo "No available sections for enrollment {$enrollment->enrollment_id} (student already in all sections)\n";
        }
    }
}

echo "\nDone! Checking final counts...\n";
echo "Total sections: " . DB::table('tblsection')->where('is_deleted', 0)->count() . "\n";
echo "Total enrollments: " . DB::table('tblenrollment')->where('is_deleted', 0)->count() . "\n";

$orphaned = DB::table('tblenrollment')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->where('tblenrollment.is_deleted', 0)
    ->whereNull('tblsection.section_id')
    ->count();

echo "Orphaned enrollments remaining: $orphaned\n";