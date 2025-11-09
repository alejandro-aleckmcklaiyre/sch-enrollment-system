<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::select('SELECT student_id, student_no, last_name, first_name FROM tblstudent LIMIT 20');
foreach ($rows as $r) {
    echo "{$r->student_id}\t{$r->student_no}\t{$r->last_name}\t{$r->first_name}\n";
}
