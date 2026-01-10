<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Existing sections:\n";
$sections = DB::table('tblsection')->where('is_deleted', 0)->get();
foreach($sections as $s) {
    echo "ID: {$s->section_id}, Code: {$s->section_code}, Course: {$s->course_id}, Term: {$s->term_id}\n";
}

echo "\nSample enrollments and their section_ids:\n";
$enrollments = DB::table('tblenrollment')->where('is_deleted', 0)->limit(10)->get();
foreach($enrollments as $e) {
    echo "Enrollment {$e->enrollment_id}: section_id = {$e->section_id}\n";
}

echo "\nChecking for orphaned enrollments:\n";
$orphaned = DB::table('tblenrollment')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->where('tblenrollment.is_deleted', 0)
    ->whereNull('tblsection.section_id')
    ->count();

echo "Orphaned enrollments (no matching section): $orphaned\n";