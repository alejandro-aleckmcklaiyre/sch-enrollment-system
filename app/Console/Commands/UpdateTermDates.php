<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Term;

class UpdateTermDates extends Command
{
    protected $signature = 'app:update-term-dates';
    protected $description = 'Update term dates to make one current';

    public function handle()
    {
        $term = Term::where('is_deleted', 0)->first();
        
        if (!$term) {
            $this->error('No terms found');
            return;
        }

        $term->start_date = '2024-01-01';
        $term->end_date = '2024-12-31';
        $term->save();

        $this->info("Updated term {$term->term_code} dates to 2024-01-01 to 2024-12-31");
    }
}
