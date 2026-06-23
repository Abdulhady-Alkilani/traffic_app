<?php

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Events\ReportCreated;
use App\Listeners\AnalyzeReportWithAi;
use App\Models\AdminData;
use App\Models\CitizenData;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use App\Services\AiService;
use App\Services\ReportAiAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeCitizenForAi(string $name = 'AI Test Citizen'): CitizenData
{
    $user = User::create([
        'username' => strtolower(str_replace(' ', '.', $name)),
        'email' => str_replace(' ', '.', $name) . '@ai.test',
        'password' => 'password',
    ]);

    return CitizenData::create([
        'user_id' => $user->id,
        'national_id' => '0000' . random_int(1000, 9999),
        'full_name' => $name,
        'phone' => '099' . random_int(1000000, 9999999),
        'blood_type' => 'O+',
    ]);
}

function makeReportForAi(array $overrides = []): Report
{
    $citizen = array_key_exists('citizen', $overrides)
        ? $overrides['citizen']
        : makeCitizenForAi();

    return Report::create(array_merge([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::TrafficPolice,
        'report_type' => 'accident',
        'description' => 'حادث تصادم بين سيارتين',
        'latitude' => 33.5138,
        'longitude' => 36.2765,
        'location_text' => 'دمشق - أوتستراد المزة',
        'status' => ReportStatus::New,
    ], $overrides));
}

function mockAiServiceForAnalysis(string $analysisJson, ?string $duplicateJson = null): AiService
{
    $mock = Mockery::mock(AiService::class);

    $callIndex = 0;
    $mock->shouldReceive('chat')
        ->andReturnUsing(function () use (&$callIndex, $analysisJson, $duplicateJson) {
            $callIndex++;
            // First call = comprehensive analysis, subsequent = duplicate confirmation
            return $callIndex === 1 ? $analysisJson : ($duplicateJson ?? '{"is_duplicate": false}');
        });

    $mock->shouldReceive('buildImageContent')->andReturn(['type' => 'image_url', 'image_url' => ['url' => 'data:image/jpeg;base64,abc']]);
    $mock->shouldReceive('buildVideoContent')->andReturn(['type' => 'image_url', 'image_url' => ['url' => 'data:video/mp4;base64,def']]);

    app()->instance(AiService::class, $mock);

    return $mock;
}

it('updates report fields from a valid AI analysis response', function () {
    $report = makeReportForAi();

    mockAiServiceForAnalysis(json_encode([
        'detected_plate' => 'دمشك 12345',
        'incident_type' => 'accident',
        'severity_score' => 4,
        'damage_assessment' => 'أضرار متوسطة في المقدمة',
        'summary' => 'حادث تصادم بسيط',
    ]));

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh())
        ->ai_detected_plate->toBe('دمشك 12345')
        ->ai_incident_type->toBe('accident')
        ->ai_severity_score->toBe(4)
        ->ai_damage_assessment->toBe('أضرار متوسطة في المقدمة')
        ->ai_summary->toBe('حادث تصادم بسيط')
        ->ai_analyzed_at->not->toBeNull();
});

it('analyzes text-only reports without media attachments', function () {
    $report = makeReportForAi(['image_url' => null, 'video_url' => null]);

    mockAiServiceForAnalysis(json_encode([
        'detected_plate' => null,
        'incident_type' => 'accident',
        'severity_score' => 3,
        'damage_assessment' => 'لا يمكن التقييم بدون صورة',
        'summary' => 'بلاغ نصي',
    ]));

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh())
        ->ai_severity_score->toBe(3)
        ->ai_summary->toBe('بلاغ نصي')
        ->ai_analyzed_at->not->toBeNull();
});

it('clamps severity score to valid range 1-5', function () {
    $report = makeReportForAi();

    mockAiServiceForAnalysis(json_encode([
        'detected_plate' => null,
        'incident_type' => 'accident',
        'severity_score' => 9,
        'damage_assessment' => 'x',
        'summary' => 'y',
    ]));

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh()->ai_severity_score)->toBe(5);
});

it('handles negative severity by clamping to 1', function () {
    $report = makeReportForAi();

    mockAiServiceForAnalysis(json_encode([
        'detected_plate' => null,
        'incident_type' => 'accident',
        'severity_score' => -2,
        'damage_assessment' => 'x',
        'summary' => 'y',
    ]));

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh()->ai_severity_score)->toBe(1);
});

it('does not break when the AI API returns null', function () {
    $report = makeReportForAi();

    $mock = Mockery::mock(AiService::class);
    $mock->shouldReceive('chat')->andReturn(null);
    $mock->shouldReceive('buildImageContent')->andReturn(null);
    $mock->shouldReceive('buildVideoContent')->andReturn(null);
    app()->instance(AiService::class, $mock);

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh()->ai_analyzed_at)->toBeNull();
});

it('parses AI responses wrapped in markdown code blocks', function () {
    $report = makeReportForAi();

    $json = json_encode([
        'detected_plate' => 'ABC 123',
        'incident_type' => 'hazard',
        'severity_score' => 2,
        'damage_assessment' => 'none',
        'summary' => 'ok',
    ]);

    mockAiServiceForAnalysis("```json\n" . $json . "\n```");

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh())
        ->ai_detected_plate->toBe('ABC 123')
        ->ai_incident_type->toBe('hazard')
        ->ai_severity_score->toBe(2);
});

it('parses AI responses wrapped in plain code blocks', function () {
    $report = makeReportForAi();

    $json = json_encode([
        'detected_plate' => null,
        'incident_type' => 'accident',
        'severity_score' => 1,
        'damage_assessment' => 'none',
        'summary' => 'minor',
    ]);

    mockAiServiceForAnalysis("```\n" . $json . "\n```");

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh()->ai_severity_score)->toBe(1);
});

it('handles malformed JSON without crashing', function () {
    $report = makeReportForAi();

    mockAiServiceForAnalysis('This is not JSON {{{');

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh()->ai_analyzed_at)->toBeNull();
});

it('detects a duplicate of a nearby recent report', function () {
    $original = makeReportForAi([
        'report_type' => 'accident',
        'latitude' => 33.5138,
        'longitude' => 36.2765,
        'description' => 'حادث في المزة',
    ]);

    $duplicate = makeReportForAi([
        'citizen' => $original->citizen,
        'report_type' => 'accident',
        'latitude' => 33.5139,
        'longitude' => 36.2766,
        'description' => 'تصادم قرب المزة',
    ]);

    mockAiServiceForAnalysis(
        json_encode([
            'detected_plate' => null,
            'incident_type' => 'accident',
            'severity_score' => 3,
            'damage_assessment' => 'x',
            'summary' => 'y',
        ]),
        json_encode(['is_duplicate' => true])
    );

    app(ReportAiAnalyzer::class)->analyze($duplicate);

    $fresh = $duplicate->fresh();
    expect($fresh)
        ->ai_is_duplicate->toBeTrue()
        ->ai_duplicate_of->toBe($original->id);
});

it('marks non-duplicate when AI confirms distinct reports', function () {
    $original = makeReportForAi([
        'report_type' => 'accident',
        'latitude' => 33.5138,
        'longitude' => 36.2765,
        'description' => 'حادث في المزة',
    ]);

    $other = makeReportForAi([
        'citizen' => $original->citizen,
        'report_type' => 'accident',
        'latitude' => 33.5140,
        'longitude' => 36.2767,
        'description' => 'حادث مختلف تماماً',
    ]);

    mockAiServiceForAnalysis(
        json_encode([
            'detected_plate' => null,
            'incident_type' => 'accident',
            'severity_score' => 3,
            'damage_assessment' => 'x',
            'summary' => 'y',
        ]),
        json_encode(['is_duplicate' => false])
    );

    app(ReportAiAnalyzer::class)->analyze($other);

    expect($other->fresh())
        ->ai_is_duplicate->toBeFalse()
        ->ai_duplicate_of->toBeNull();
});

it('skips duplicate detection when report has no coordinates', function () {
    $report = makeReportForAi([
        'latitude' => null,
        'longitude' => null,
    ]);

    mockAiServiceForAnalysis(json_encode([
        'detected_plate' => null,
        'incident_type' => 'accident',
        'severity_score' => 2,
        'damage_assessment' => 'x',
        'summary' => 'y',
    ]));

    app(ReportAiAnalyzer::class)->analyze($report);

    expect($report->fresh())
        ->ai_is_duplicate->toBeFalse()
        ->ai_duplicate_of->toBeNull();
});

/*
 * Listener tests
 */

it('listener skips reports with no content', function () {
    $citizen = makeCitizenForAi();
    // description column is NOT NULL, use empty string to simulate "no content"
    $report = Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::TrafficPolice,
        'report_type' => 'accident',
        'description' => '',
        'status' => ReportStatus::New,
    ]);

    $analyzerMock = Mockery::mock(ReportAiAnalyzer::class);
    $analyzerMock->shouldNotReceive('analyze');
    app()->instance(ReportAiAnalyzer::class, $analyzerMock);

    // Resolve listener AFTER binding mock so constructor injection uses the mock
    $listener = app(AnalyzeReportWithAi::class);
    $listener->handle(new ReportCreated($report));
});

it('listener triggers analysis for reports with a description', function () {
    $report = makeReportForAi(['image_url' => null, 'video_url' => null]);

    $analyzerMock = Mockery::mock(ReportAiAnalyzer::class);
    $analyzerMock->shouldReceive('analyze')->once()->with(Mockery::on(fn ($r) => $r->is($report)));
    app()->instance(ReportAiAnalyzer::class, $analyzerMock);

    // Resolve listener AFTER binding mock so constructor injection uses the mock
    $listener = app(AnalyzeReportWithAi::class);
    $listener->handle(new ReportCreated($report));
});

it('listener catches exceptions without breaking', function () {
    $report = makeReportForAi();

    $analyzerMock = Mockery::mock(ReportAiAnalyzer::class);
    $analyzerMock->shouldReceive('analyze')->andThrow(new \RuntimeException('AI down'));
    app()->instance(ReportAiAnalyzer::class, $analyzerMock);

    $listener = new AnalyzeReportWithAi(app(ReportAiAnalyzer::class));

    // Should not throw — listener swallows the exception
    $listener->handle(new ReportCreated($report));

    expect($report->fresh())->not->toBeNull();
});

/*
 * Event wiring test
 */

it('dispatches ReportCreated event that reaches the AI listener', function () {
    // Create an admin so LogReportCreation's FK on activity_logs.admin_id is satisfied
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $adminUser = User::create([
        'username' => 'admin',
        'email' => 'admin@ai.test',
        'password' => 'password',
        'role_id' => $role->id,
        'is_active' => true,
    ]);
    AdminData::create(['user_id' => $adminUser->id, 'full_name' => 'Site Admin']);

    // Bind a mock analyzer so we don't make real HTTP calls
    $analyzerMock = Mockery::mock(ReportAiAnalyzer::class);
    $analyzerMock->shouldReceive('analyze')->once();
    app()->instance(ReportAiAnalyzer::class, $analyzerMock);

    $citizen = makeCitizenForAi();
    $report = Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::TrafficPolice,
        'report_type' => 'accident',
        'description' => 'حدث حادث',
        'status' => ReportStatus::New,
    ]);

    ReportCreated::dispatch($report);
});

/*
 * AiService content building tests
 */

it('builds image content from a real file as base64 data URI', function () {
    Storage::fake('public');

    $imageContent = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAAAv/EABQQAQAAAAAAAAAAAAAAAAAAAAD/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AvwA=');
    $file = 'reports/test-image.jpg';
    Storage::disk('public')->put($file, $imageContent);

    $service = app(AiService::class);
    $result = $service->buildImageContent($file);

    expect($result)
        ->toBeArray()
        ->and($result['type'])->toBe('image_url')
        ->and($result['image_url']['url'])->toStartWith('data:image/');

    Storage::disk('public')->delete($file);
});

it('returns null when image file does not exist', function () {
    $service = app(AiService::class);
    expect($service->buildImageContent('reports/nonexistent.jpg'))->toBeNull();
});

it('returns null when video file does not exist', function () {
    $service = app(AiService::class);
    expect($service->buildVideoContent('reports/nonexistent.mp4'))->toBeNull();
});

/*
 * Model integration tests
 */

it('casts AI fields correctly on the Report model', function () {
    $report = makeReportForAi();

    $report->update([
        'ai_severity_score' => '4',
        'ai_is_duplicate' => 1,
        'ai_analyzed_at' => '2026-06-23 10:00:00',
    ]);

    expect($report->fresh())
        ->ai_severity_score->toBeInt()
        ->ai_is_duplicate->toBeBool()->toBeTrue()
        ->ai_analyzed_at->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('resolves the duplicateOf relationship', function () {
    $original = makeReportForAi();
    $duplicate = makeReportForAi(['citizen' => $original->citizen]);

    $duplicate->update(['ai_duplicate_of' => $original->id]);

    expect($duplicate->fresh()->duplicateOf)
        ->not->toBeNull()
        ->id->toBe($original->id);
});
