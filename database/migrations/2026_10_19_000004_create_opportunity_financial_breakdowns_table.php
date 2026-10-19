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
        Schema::create('opportunity_financial_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->string('item_name'); // e.g., "Initial Franchise Fee", "Kitchen Equipment", "Interior Fit-out"
            $table->unsignedBigInteger('amount')->nullable();
            $table->boolean('is_estimate')->default(false);
            $table->boolean('is_refundable')->default(false);
            $table->string('source_evidence')->nullable(); // Provenance citation: Agreement Clause, Price List date
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['opportunity_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunity_financial_breakdowns');
    }
};
