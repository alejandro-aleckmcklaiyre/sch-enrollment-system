<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Fixing enrollments with null/empty section_ids...\n";

// Get valid sections
$validSections = DB::table('tblsection')->where('is_deleted', 0)->pluck('section_id')->toArray();

if (empty($validSections)) {
    echo "No valid sections found!\n";
    exit;
}

// Find enrollments with null or empty section_id
$nullEnrollments = DB::table('tblenrollment')
    ->where('is_deleted', 0)
    ->where(function($query) {
        $query->whereNull('section_id')
              ->orWhere('section_id', '');
    })
    ->get();

echo "Found {$nullEnrollments->count()} enrollments with null/empty section_id\n";

foreach($nullEnrollments as $enrollment) {
    // Find a section this student is not already enrolled in
    $existingSections = DB::table('tblenrollment')
        ->where('student_id', $enrollment->student_id)
        ->where('is_deleted', 0)
        ->whereNotNull('section_id')
        ->where('section_id', '!=', '')
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
$orphaned = DB::table('tblenrollment')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->where('tblenrollment.is_deleted', 0)
    ->whereNull('tblsection.section_id')
    ->count();

echo "Orphaned enrollments remaining: $orphaned\n";