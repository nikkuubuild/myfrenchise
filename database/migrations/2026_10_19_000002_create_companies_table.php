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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('brand_name');
            $table->string('slug')->unique();
            $table->string('cin', 21)->nullable()->index(); // MCA Corporate Identification Number
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->text('registered_address')->nullable();
            $table->string('operational_state')->nullable();
            $table->string('official_website')->nullable();
            $table->string('official_email')->nullable();
            $table->string('official_phone')->nullable();
            $table->unsignedSmallInteger('incorporation_year')->nullable();
            $table->string('status', 30)->default('draft'); // draft, under_review, verified, rejected, suspended
            $table->timestamps();

            $table->index(['status', 'brand_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
