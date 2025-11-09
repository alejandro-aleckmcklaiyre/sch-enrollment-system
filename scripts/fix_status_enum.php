<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Altering status column to VARCHAR(20)...\n";
    DB::statement("ALTER TABLE tblenrollment MODIFY status VARCHAR(20) NOT NULL DEFAULT 'enrolled'");
    echo "Lowercasing existing status values...\n";
    DB::statement("UPDATE tblenrollment SET status = LOWER(status) WHERE status IS NOT NULL");
    echo "Altering status column to ENUM(...) with lowercase values...\n";
    DB::statement("ALTER TABLE tblenrollment MODIFY status ENUM('enrolled','dropped','completed','irregular') NOT NULL DEFAULT 'enrolled'");
    echo "Done.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
