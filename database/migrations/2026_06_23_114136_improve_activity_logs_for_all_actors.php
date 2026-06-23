<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->foreignId('admin_id')->nullable()->change()->constrained('admins_data')->nullOnDelete();
            $table->string('actor_type')->nullable()->after('admin_id');
            $table->string('actor_name')->nullable()->after('actor_type');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['actor_type', 'actor_name']);
            $table->dropForeign(['admin_id']);
            $table->foreignId('admin_id')->nullable(false)->change()->constrained('admins_data')->cascadeOnDelete();
        });
    }
};
