<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Orphaned enrollments section_ids:\n";
$orphaned = DB::table('tblenrollment')
    ->leftJoin('tblsection', 'tblenrollment.section_id', '=', 'tblsection.section_id')
    ->where('tblenrollment.is_deleted', 0)
    ->whereNull('tblsection.section_id')
    ->select('tblenrollment.enrollment_id', 'tblenrollment.section_id')
    ->limit(20)
    ->get();

foreach($orphaned as $e) {
    echo "Enrollment {$e->enrollment_id}: section_id = {$e->section_id}\n";
}