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
        Schema::create('verification_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_case_id')->constrained('verification_cases')->cascadeOnDelete();
            $table->string('check_type', 60); // company_identity, contact_authority, opportunity_authorization, opportunity_status, territory_availability, financial_disclosures, infrastructure_licence, contract_terms
            $table->string('title');
            $table->boolean('is_passed')->default(false);
            $table->boolean('is_mandatory')->default(true);
            $table->text('evidence_source'); // Provenance: MCA lookup reference, Agreement Clause, Official Notice
            $table->string('evidence_file_path')->nullable(); // Stored securely in storage/app/private/evidence/
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('checked_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();

            $table->index(['verification_case_id', 'check_type']);
            $table->index(['verification_case_id', 'is_passed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_checks');
    }
};
