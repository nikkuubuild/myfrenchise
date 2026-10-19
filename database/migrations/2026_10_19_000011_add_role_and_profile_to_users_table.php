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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('customer')->after('password'); // customer, case_manager, verification_officer, admin
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('state', 10)->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('state');
            $table->boolean('is_active')->default(true)->after('city');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            $table->index(['role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
            $table->dropColumn(['role', 'phone', 'state', 'city', 'is_active', 'last_login_at']);
        });
    }
};
