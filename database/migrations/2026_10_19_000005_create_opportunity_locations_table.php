<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opportunity_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->string('state_code', 10); // e.g. "UP", "MH", "DL"
            $table->string('state_name', 100); // e.g. "Uttar Pradesh"
            $table->string('district', 100)->nullable(); // e.g. "Lucknow"
            $table->string('territory_tier', 30)->nullable()->default('Tier 2'); // Tier 1, Tier 2, Tier 3, Pan-India
            $table->boolean('is_exclusive')->default(false); // Territorial radius protection exists
            $table->string('status', 30)->default('available'); // available, waitlist, closed
            $table->timestamps();

            $table->index(['state_code', 'status']);
            $table->index(['opportunity_id', 'state_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunity_locations');
    }
};
