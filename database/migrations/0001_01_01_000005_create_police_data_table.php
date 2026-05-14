<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('police_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('badge_number')->unique();
            $table->string('full_name');
            $table->string('rank');
            $table->enum('department', ['highway_patrol', 'traffic_police', 'local_police']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('police_data');
    }
};