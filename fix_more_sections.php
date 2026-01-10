<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Creating more sections...\n";

// Get some courses, terms, instructors, and rooms
$courses = DB::table('tblcourse')->where('is_deleted', 0)->get();
$terms = DB::table('tblterm')->where('is_deleted', 0)->get();
$instructors = DB::table('tblinstructor')->where('is_deleted', 0)->get();
$rooms = DB::table('tblroom')->where('is_deleted', 0)->get();

// Get the next section ID
$maxSectionId = DB::table('tblsection')->max('section_id') ?? 0;
$sectionId = $maxSectionId + 1;

// Create more sections - multiple sections per course-term combo
foreach($courses as $course) {
    foreach($terms as $term) {
        // Create 2-3 sections for each course-term combo
        for($i = 0; $i < 3; $i++) {
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

            $sectionId++;

            if ($sectionId > 200) break 3; // Limit to reasonable number
        }
    }
}

echo "Created sections up to ID " . ($sectionId - 1) . "\n";

echo "\nUpdating remaining orphaned enrollments...\n";

// Update remaining orphaned enrollments
$validSections = DB::table('tblsection')->where('is_deleted', 0)->pluck('section_id')->toArray();

$orphanedEnrollments = DB::table('tblenrollment')
    ->where('is_deleted', 0)
    ->whereNotIn('section_id', $validSections)
    ->get();

echo "Found " . $orphanedEnrollments->count() . " orphaned enrollments\n";

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
        echo "No available sections for enrollment {$enrollment->enrollment_id}\n";
    }
}

echo "\nFinal check...\n";
echo "Total sections: " . DB::table('tblsection')->where('is_deleted', 0)->count() . "\n";
echo "Total enrollments: " . DB::table('tblenrollment')->where('is_deleted', 0)->count() . "\n";

$orphaned = DB::table('tblenrollment')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->where('tblenrollment.is_deleted', 0)
    ->whereNull('tblsection.section_id')
    ->count();

echo "Orphaned enrollments remaining: $orphaned\n";