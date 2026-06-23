<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('ai_detected_plate')->nullable()->after('status');
            $table->string('ai_incident_type')->nullable()->after('ai_detected_plate');
            $table->tinyInteger('ai_severity_score')->nullable()->after('ai_incident_type');
            $table->text('ai_damage_assessment')->nullable()->after('ai_severity_score');
            $table->text('ai_summary')->nullable()->after('ai_damage_assessment');
            $table->boolean('ai_is_duplicate')->default(false)->after('ai_summary');
            $table->foreignId('ai_duplicate_of')->nullable()->after('ai_is_duplicate')
                ->constrained('reports')->nullOnDelete();
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_duplicate_of');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['ai_duplicate_of']);
            $table->dropColumn([
                'ai_detected_plate',
                'ai_incident_type',
                'ai_severity_score',
                'ai_damage_assessment',
                'ai_summary',
                'ai_is_duplicate',
                'ai_duplicate_of',
                'ai_analyzed_at',
            ]);
        });
    }
};
