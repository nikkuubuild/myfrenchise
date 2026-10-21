<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Automatically populates baseline categories, verified sample opportunities,
     * due diligence articles, service packages, and staff credentials
     * whenever the database is migrated in non-testing environments.
     */
    public function up(): void
    {
        if (! app()->environment('testing')) {
            Artisan::call('db:seed', [
                '--class' => DatabaseSeeder::class,
                '--force' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reference master data preserved on rollback
    }
};
