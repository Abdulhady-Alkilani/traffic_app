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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('model_name')->nullable()->after('make');
            $table->string('chassis_number')->nullable()->unique()->after('model_year');
            $table->string('engine_number')->nullable()->after('chassis_number');
            $table->date('registration_expiry')->nullable()->after('color');
            $table->enum('insurance_status', ['valid', 'expired'])->default('valid')->after('registration_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'model_name',
                'chassis_number',
                'engine_number',
                'registration_expiry',
                'insurance_status',
            ]);
        });
    }
};
