<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CleanupData extends Command
{
    protected $signature = 'app:cleanup-data';

    protected $description = 'Delete all records from advertisers, founders, rfcs, startup_gross_revenues, startup_monthly_mrrs, and startups tables';

    public function handle(): int
    {
        if (! $this->confirm('This will delete all records from multiple tables. Are you sure?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        DB::transaction(function (): void {
            $tables = [
                'advertisers',
                'founders',
                'rfcs',
                'startup_gross_revenues',
                'startup_monthly_mrrs',
                'startups',
            ];

            foreach ($tables as $table) {
                DB::table($table)->truncate();
                $this->info("Truncated table: {$table}");
            }
        });

        $this->info('Cleanup completed successfully.');

        return 0;
    }
}
