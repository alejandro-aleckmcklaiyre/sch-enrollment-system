<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::select('SELECT section_id, section_code, course_id, is_deleted FROM tblsection LIMIT 20');
foreach ($rows as $r) {
    echo "{$r->section_id}\t{$r->section_code}\t{$r->course_id}\t{$r->is_deleted}\n";
}
