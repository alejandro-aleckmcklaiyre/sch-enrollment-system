<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking sections 3,4,20,133,196...\n";

$sections = DB::table('tblsection')->whereIn('section_id', [3,4,20,133,196])->get();

foreach ($sections as $section) {
    echo "Section {$section->section_id}: course_id={$section->course_id}, instructor_id={$section->instructor_id}, room_id={$section->room_id}\n";
    
    $course = DB::table('tblcourse')->where('course_id', $section->course_id)->first();
    echo "  Course: " . ($course ? $course->course_title : "NOT FOUND") . "\n";
    
    $instructor = DB::table('tblinstructor')->where('instructor_id', $section->instructor_id)->first();
    echo "  Instructor: " . ($instructor ? $instructor->first_name . ' ' . $instructor->last_name : "NOT FOUND") . "\n";
    
    $room = DB::table('tblroom')->where('room_id', $section->room_id)->first();
    echo "  Room: " . ($room ? $room->room_code : "NOT FOUND") . "\n";
    echo "---\n";
}