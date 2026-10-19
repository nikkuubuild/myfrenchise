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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 20); // mail, sms_dlt, in_app
            $table->string('recipient'); // email or mobile number
            $table->string('event_type', 60); // case_stage_updated, consent_requested, payment_successful, grievance_status_updated
            $table->string('subject')->nullable();
            $table->text('message_body');
            $table->string('dlt_template_id', 50)->nullable(); // TRAI / DLT regulatory template identifier
            $table->string('status', 20)->default('delivered'); // queued, delivered, failed
            $table->string('provider_message_id')->nullable();
            $table->text('error_details')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'event_type', 'created_at']);
            $table->index(['channel', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
