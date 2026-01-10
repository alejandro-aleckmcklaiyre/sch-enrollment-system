<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$terms = DB::table('tblterm')->where('is_deleted', 0)->get();

echo "Terms:\n";
foreach ($terms as $term) {
    echo "{$term->term_code}: {$term->start_date} to {$term->end_date}\n";
}

$currentTerm = DB::table('tblterm')
    ->where('is_deleted', 0)
    ->where('start_date', '<=', now()->toDateString())
    ->where('end_date', '>=', now()->toDateString())
    ->first();

if ($currentTerm) {
    echo "\nCurrent term: {$currentTerm->term_code}\n";
} else {
    echo "\nNo current term\n";
}
