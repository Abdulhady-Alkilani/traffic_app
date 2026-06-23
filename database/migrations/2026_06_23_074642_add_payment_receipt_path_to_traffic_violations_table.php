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
        Schema::table('traffic_violations', function (Blueprint $table) {
            $table->string('payment_receipt_path')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traffic_violations', function (Blueprint $table) {
            $table->dropColumn('payment_receipt_path');
        });
    }
};
