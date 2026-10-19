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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->string('business_model', 50)->default('FOFO'); // FOFO, FOCO, COCO, Dealership, Distributorship
            
            // Financial Outlay in Indian Rupees (INR)
            $table->unsignedBigInteger('min_investment'); // e.g. 850000
            $table->unsignedBigInteger('max_investment'); // e.g. 1200000
            $table->unsignedBigInteger('franchise_fee')->nullable();
            $table->decimal('royalty_percentage', 5, 2)->nullable();
            
            // Infrastructure Requirements
            $table->unsignedInteger('min_space_sqft')->nullable();
            $table->unsignedInteger('max_space_sqft')->nullable();
            $table->unsignedSmallInteger('min_term_years')->nullable()->default(5);
            
            // Lifecycle and Verification State
            $table->string('publication_status', 30)->default('draft'); // draft, under_review, published, unpublished, suspended
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('verification_valid_until')->nullable();
            
            // Provenance & Legal Caveat
            $table->text('disclaimer')->nullable();
            $table->timestamps();

            // Indexes for Search & Filter
            $table->index(['publication_status', 'category_id']);
            $table->index(['min_investment', 'max_investment']);
            $table->index('last_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
