<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$studentId = $argv[1] ?? 20;
$sectionId = $argv[2] ?? 11;
$courseId = $argv[3] ?? 1;
$date = date('Y-m-d');
$status = 'ENROLLED';

try {
    $affected = DB::insert('INSERT INTO tblenrollment (student_id, section_id, course_id, date_enrolled, status, is_deleted) VALUES (?, ?, ?, ?, ?, 0)', [$studentId, $sectionId, $courseId, $date, $status]);
    echo "Inserted: ";
    var_export($affected);
    echo "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
