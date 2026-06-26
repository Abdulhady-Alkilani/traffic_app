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
        Schema::table('reports', function (Blueprint $table) {
            $table->index('created_at', 'reports_created_at_index');
        });

        Schema::table('traffic_violations', function (Blueprint $table) {
            $table->index('issued_at', 'traffic_violations_issued_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_created_at_index');
        });

        Schema::table('traffic_violations', function (Blueprint $table) {
            $table->dropIndex('traffic_violations_issued_at_index');
        });
    }
};
