<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::getSchemaBuilder()->getColumnListing('tblroom');
echo 'Room columns: ' . implode(', ', $columns) . '\n';

$rooms = DB::table('tblroom')->where('is_deleted', 0)->limit(3)->get();
foreach($rooms as $r) {
    echo "ID: {$r->room_id}, Code: " . ($r->room_code ?? 'NULL') . "\n";
}