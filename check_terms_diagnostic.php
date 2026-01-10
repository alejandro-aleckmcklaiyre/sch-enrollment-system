<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\Term;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== TERM DIAGNOSTIC ===\n\n";

$allTerms = Term::all();
echo "Total terms in database: " . $allTerms->count() . "\n\n";

if ($allTerms->count() > 0) {
    echo "All terms:\n";
    foreach ($allTerms as $term) {
        echo "- ID: {$term->term_id}, Code: {$term->term_code}, Name: {$term->term_name}, Start: {$term->start_date}, End: {$term->end_date}, Deleted: {$term->is_deleted}\n";
    }
} else {
    echo "No terms found!\n";
}

echo "\n=== CURRENT DATE CHECK ===\n";
$today = now()->toDateString();
echo "Today's date: {$today}\n";

$currentTerm = Term::getCurrentTerm();
if ($currentTerm) {
    echo "Current term found: {$currentTerm->term_name} (ID: {$currentTerm->term_id})\n";
} else {
    echo "No current term found!\n";
    echo "Checking why...\n";

    // Check terms that are not deleted
    $activeTerms = Term::where('is_deleted', 0)->get();
    echo "Active terms: " . $activeTerms->count() . "\n";

    foreach ($activeTerms as $term) {
        $start = $term->start_date;
        $end = $term->end_date;
        $isCurrent = ($start <= $today && $end >= $today);
        echo "- {$term->term_name}: {$start} to {$end} - " . ($isCurrent ? 'CURRENT' : 'NOT CURRENT') . "\n";
    }
}

echo "\n=== DONE ===\n";