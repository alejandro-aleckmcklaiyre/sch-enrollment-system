<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the database connection using configured DB settings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = microtime(true);

        try {
            $result = DB::select('SELECT 1 as ok');

            $elapsed = round((microtime(true) - $start) * 1000, 2);

            $payload = [
                'status' => 'success',
                'message' => 'Database connection successful',
                'result' => $result,
                'elapsed_ms' => $elapsed,
            ];

            $this->line(json_encode($payload, JSON_PRETTY_PRINT));

            return 0;
        } catch (\Exception $e) {
            $elapsed = round((microtime(true) - $start) * 1000, 2);

            $payload = [
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
                'elapsed_ms' => $elapsed,
            ];

            $this->line(json_encode($payload, JSON_PRETTY_PRINT));

            return 1;
        }
    }
}
