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
        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_to_user_id');
            $table->string('priority', 20)->default('normal')->after('status'); // normal, high, urgent
            $table->timestamp('next_follow_up_at')->nullable()->after('priority');
            $table->string('stage', 30)->default('new')->after('next_follow_up_at'); // new, intake_assessment, shortlist_prepared, consent_pending, submitted_to_brand, brand_interview, closed_won, closed_lost
            
            $table->index(['assigned_to_user_id', 'stage']);
            $table->index(['priority', 'next_follow_up_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropIndex(['assigned_to_user_id', 'stage']);
            $table->dropIndex(['priority', 'next_follow_up_at']);
            $table->dropForeign(['assigned_to_user_id']);
            $table->dropColumn(['assigned_to_user_id', 'assigned_at', 'priority', 'next_follow_up_at', 'stage']);
        });
    }
};
