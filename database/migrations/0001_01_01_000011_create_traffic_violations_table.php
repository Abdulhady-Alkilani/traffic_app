<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_violations', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('citizen_id')->constrained('citizens_data')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('police_id')->constrained('police_data')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('reports')->cascadeOnDelete();
            $table->string('violation_type');
            $table->text('description')->nullable();
            $table->decimal('fine_amount', 8, 2);
            $table->string('status')->default('unpaid');
            $table->timestamp('issued_at')->default(now());
            $table->date('due_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_violations');
    }
};
