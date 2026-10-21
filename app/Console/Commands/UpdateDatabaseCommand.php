<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-db {--force : Force the operation in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate schema, automatically seed/sync baseline production data, and optimize caches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Franchise Dekho Database Update & Automated Data Sync...');
        $this->newLine();

        // 1. Run migrations (which includes automated baseline seeding migration)
        $this->info('1. Executing database migrations...');
        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ], $this->getOutput());

        if ($exitCode !== 0) {
            $this->error('Database migration failed.');
            return $exitCode;
        }

        // 2. Sync baseline master data
        $this->info('2. Verifying and syncing master baseline data (categories, verified opportunities, packages, guides)...');
        $exitCode = Artisan::call('db:seed', [
            '--class' => \Database\Seeders\DatabaseSeeder::class,
            '--force' => true,
        ], $this->getOutput());

        if ($exitCode !== 0) {
            $this->error('Database seeding sync failed.');
            return $exitCode;
        }

        $this->newLine();
        $this->info('Database update and automated data synchronization completed successfully.');

        return Command::SUCCESS;
    }
}
