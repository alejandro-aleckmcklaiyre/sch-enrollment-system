<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Fixing sections with invalid course_id = 0...\n";

// Get valid course IDs
$validCourses = DB::table('tblcourse')->where('is_deleted', 0)->pluck('course_id')->toArray();

if (empty($validCourses)) {
    echo "No valid courses found!\n";
    exit(1);
}

echo "Found " . count($validCourses) . " valid courses\n";

// Find sections with course_id = 0 or null
$invalidSections = DB::table('tblsection')
    ->where('is_deleted', 0)
    ->where(function($query) {
        $query->where('course_id', 0)
              ->orWhereNull('course_id');
    })
    ->get();

echo "Found {$invalidSections->count()} sections with invalid course_id\n";

foreach ($invalidSections as $section) {
    $randomCourseId = $validCourses[array_rand($validCourses)];
    
    DB::table('tblsection')
        ->where('section_id', $section->section_id)
        ->update(['course_id' => $randomCourseId]);
    
    echo "Updated section {$section->section_id} (code: {$section->section_code}) to use course {$randomCourseId}\n";
}

echo "\nDone!\n";