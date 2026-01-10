<?php
require_once 'vendor/autoload.php';
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('tblenrollment');
echo "Columns in tblenrollment:\n";
foreach ($columns as $column) {
    echo "- $column\n";
}
?>