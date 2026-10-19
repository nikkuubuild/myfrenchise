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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 30)->unique(); // e.g. FD-CASE-202610-0012
            $table->foreignId('opportunity_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone', 20);
            $table->string('email');
            $table->string('target_state', 10); // e.g. "UP", "MH"
            $table->string('target_city', 100);
            $table->string('budget_range', 30); // under-5l, 5l-15l, 15l-30l, 30l-plus
            $table->string('preferred_timeline', 30)->default('1-3-months'); // immediate, 1-3-months, 3-6-months
            $table->string('commercial_space_status', 30)->default('searching'); // owned, leased, searching
            $table->unsignedInteger('carpet_area_sqft')->nullable();
            $table->string('business_experience', 50)->default('first_time'); // first_time, existing_retail, corporate_professional, other
            $table->text('notes')->nullable();
            
            // Mandatory Consent Recording
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_recorded_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 30)->default('new'); // new, shortlisted, consented, submitted_to_brand, closed
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['target_state', 'budget_range']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
