<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$studentId = $argv[1] ?? 20;
$sectionId = $argv[2] ?? 11;
$courseId = $argv[3] ?? 1;

$res = DB::select('SELECT COUNT(*) as cnt FROM tblenrollment WHERE student_id = ? AND section_id = ? AND course_id = ? AND is_deleted = 0', [$studentId, $sectionId, $courseId]);
echo $res[0]->cnt . "\n";
