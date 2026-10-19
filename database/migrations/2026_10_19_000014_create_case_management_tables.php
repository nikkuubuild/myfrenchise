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
        // 1. Status transition history
        Schema::create('case_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 50);
            $table->string('to_status', 50);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['enquiry_id', 'created_at']);
        });

        // 2. Case tasks and checklist items
        Schema::create('case_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->date('due_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['enquiry_id', 'is_completed']);
        });

        // 3. Communications and notes with strict customer visibility boundary
        Schema::create('case_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 30)->default('internal_note'); // internal_note, call_log, customer_update, brand_communication
            $table->boolean('is_customer_visible')->default(false); // Non-negotiable boundary: false = staff only
            $table->string('subject')->nullable();
            $table->text('message');
            $table->timestamps();

            $table->index(['enquiry_id', 'is_customer_visible', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_communications');
        Schema::dropIfExists('case_tasks');
        Schema::dropIfExists('case_status_histories');
    }
};
