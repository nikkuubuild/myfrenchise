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
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 30)->unique(); // e.g. FD-GRV-202610-0001
            $table->string('reporter_type', 30); // entrepreneur, brand_owner, consumer, whistleblower
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->foreignId('opportunity_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('category', 40); // unauthorized_impersonation, misleading_financials, fee_fraud, consent_violation, service_complaint, other
            $table->string('subject');
            $table->text('description');
            $table->text('evidence_summary')->nullable();
            $table->string('status', 30)->default('received'); // received, under_investigation, resolved, rejected
            $table->string('priority', 20)->default('normal'); // normal, high, urgent
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['status', 'priority', 'created_at']);
            $table->index(['email', 'tracking_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};
