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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author_name')->default('Franchise Dekho Research Desk');
            $table->string('author_title')->default('Senior Regulatory & Franchise Due Diligence Analyst');
            $table->unsignedInteger('reading_time_minutes')->default(5);
            $table->text('excerpt');
            $table->longText('content');
            $table->text('sources_and_evidence')->nullable();
            $table->text('disclaimer')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('category_id');
            $table->index('last_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
