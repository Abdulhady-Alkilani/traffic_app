<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_id')->constrained('citizens_data')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->enum('assigned_department', ['highway_patrol', 'traffic_police', 'local_police']);
            $table->string('report_type');
            $table->text('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_text')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('status', ['new', 'in_progress', 'resolved', 'rejected'])->default('new');
            $table->timestamps();

            $table->index('latitude');
            $table->index('longitude');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};