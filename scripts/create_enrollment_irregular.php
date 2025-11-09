<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$studentId = $argv[1] ?? 33;
$sectionId = $argv[2] ?? 11;
$courseId = $argv[3] ?? 1;
$date = date('Y-m-d');
$status = 'irregular';

try {
    $exists = DB::select('SELECT COUNT(*) as cnt FROM tblenrollment WHERE student_id = ? AND section_id = ? AND course_id = ? AND is_deleted = 0', [$studentId, $sectionId, $courseId]);
    if ($exists[0]->cnt > 0) {
        echo "Already enrolled (exists = {$exists[0]->cnt}).\n";
        exit(0);
    }

    $affected = DB::insert('INSERT INTO tblenrollment (student_id, section_id, course_id, date_enrolled, status, is_deleted) VALUES (?, ?, ?, ?, ?, 0)', [$studentId, $sectionId, $courseId, $date, $status]);
    echo "Inserted: ";
    var_export($affected);
    echo "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
