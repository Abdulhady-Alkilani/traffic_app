<?php

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Enums\ViolationStatus;
use App\Models\CitizenData;
use App\Models\PoliceData;
use App\Models\Report;
use App\Models\Role;
use App\Models\TrafficViolation;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function makeCitizen(string $name = 'Citizen One'): array
{
    $user = User::create([
        'username' => strtolower(str_replace(' ', '.', $name)),
        'email' => str_replace(' ', '.', $name) . '@test.com',
        'password' => 'password',
    ]);

    $citizen = CitizenData::create([
        'user_id' => $user->id,
        'national_id' => '0000' . random_int(1000, 9999),
        'full_name' => $name,
        'phone' => '099' . random_int(1000000, 9999999),
        'blood_type' => 'O+',
    ]);

    return [$user, $citizen];
}

function makeOfficer(): PoliceData
{
    $user = User::create([
        'username' => 'officer' . random_int(1000, 9999),
        'email' => 'officer' . random_int(1000, 9999) . '@test.com',
        'password' => 'password',
    ]);

    return PoliceData::create([
        'user_id' => $user->id,
        'badge_number' => 'B' . random_int(1000, 9999),
        'full_name' => 'Officer Test',
        'rank' => 'Sergeant',
        'department' => Department::TrafficPolice->value,
    ]);
}

function createReport(CitizenData $citizen, string $status, string $location, Carbon $createdAt, Carbon $updatedAt): Report
{
    $report = Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::LocalPolice->value,
        'report_type' => 'accident',
        'description' => 'Test report',
        'location_text' => $location,
        'status' => $status,
    ]);

    DB::table('reports')->where('id', $report->id)->update([
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ]);

    return $report->refresh();
}

function createViolation(CitizenData $citizen, PoliceData $officer, Carbon $issuedAt, float $fine, string $status = 'unpaid', string $description = 'دمشق'): TrafficViolation
{
    $violation = TrafficViolation::create([
        'citizen_id' => $citizen->id,
        'police_id' => $officer->id,
        'violation_type' => 'speeding',
        'description' => $description,
        'fine_amount' => $fine,
        'status' => $status,
        'issued_at' => $issuedAt,
        'due_date' => $issuedAt->copy()->addMonth(),
    ]);

    return $violation;
}

it('calculates KPIs across a date range', function () {
    [$user, $citizen] = makeCitizen('Citizen Alpha');
    $officer = makeOfficer();

    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 6, 30);

    createReport($citizen, ReportStatus::Resolved->value, 'دمشق', Carbon::create(2026, 3, 1), Carbon::create(2026, 3, 1, 2));
    createReport($citizen, ReportStatus::New->value, 'حلب', Carbon::create(2026, 4, 1), Carbon::create(2026, 4, 1));
    createViolation($citizen, $officer, Carbon::create(2026, 4, 1, 10), 50000, 'unpaid', 'دمشق');
    createViolation($citizen, $officer, Carbon::create(2026, 5, 1, 10), 25000, 'paid', 'حلب');

    $service = app(AnalyticsService::class);
    $kpis = $service->kpis($start, $end);

    expect($kpis['total_reports'])->toBe(2)
        ->and($kpis['total_violations'])->toBe(2)
        ->and($kpis['resolution_rate'])->toBe(50.0)
        ->and($kpis['avg_response_minutes'])->toBe(120.0)
        ->and($kpis['violation_rate_per_driver'])->toBe(2.0)
        ->and($kpis['collection_rate'])->toBe(50.0)
        ->and($kpis['total_fines'])->toBe(75000.0)
        ->and($kpis['collected_fines'])->toBe(25000.0);
});

it('builds a monthly trend with portable queries', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 3, 31);

    createReport($citizen, ReportStatus::New->value, 'دمشق', Carbon::create(2026, 1, 15), Carbon::create(2026, 1, 15));
    createViolation($citizen, $officer, Carbon::create(2026, 2, 10, 9), 10000);

    $trend = app(AnalyticsService::class)->monthlyTrend($start, $end);

    expect($trend)->toHaveKeys(['2026-01', '2026-02', '2026-03'])
        ->and($trend['2026-01']['reports'])->toBe(1)
        ->and($trend['2026-02']['violations'])->toBe(1)
        ->and($trend['2026-01']['total'])->toBe(1);
});

it('extracts regions from location text', function () {
    app()->setLocale('ar');

    $service = app(AnalyticsService::class);

    expect($service->extractRegion('أوتستراد دمشق-حمص، قرب جرمانا'))->toBe('دمشق')
        ->and($service->extractRegion('حي الجميلية، حلب'))->toBe('حلب')
        ->and($service->extractRegion(null))->toBe(__('analytics.regions.unknown'));
});

it('ranks regions by compliance', function () {
    app()->setLocale('ar');

    [$user, $good] = makeCitizen('Good Region');
    [$user2, $bad] = makeCitizen('Bad Region');
    $officer = makeOfficer();

    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 6, 30);

    createReport($good, ReportStatus::Resolved->value, 'طرطوس', Carbon::create(2026, 2, 1), Carbon::create(2026, 2, 1));
    createReport($bad, ReportStatus::New->value, 'حلب', Carbon::create(2026, 2, 1), Carbon::create(2026, 2, 1));
    createViolation($bad, $officer, Carbon::create(2026, 2, 5, 8), 50000, 'unpaid', 'حلب');

    $regions = app(AnalyticsService::class)->regionCompliance($start, $end);
    $best = app(AnalyticsService::class)->bestRegions($start, $end, 1);
    $worst = app(AnalyticsService::class)->worstRegions($start, $end, 1);

    expect($best[0]['region'])->toBe('طرطوس')
        ->and($worst[0]['region'])->toBe('حلب');
});

it('compares current range with previous range', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    createViolation($citizen, $officer, Carbon::create(2026, 5, 10, 9), 50000);
    createViolation($citizen, $officer, Carbon::create(2026, 6, 10, 9), 50000);
    createViolation($citizen, $officer, Carbon::create(2026, 6, 15, 9), 50000);

    $start = Carbon::create(2026, 6, 1);
    $end = Carbon::create(2026, 6, 30);

    $comparison = app(AnalyticsService::class)->compareWithPrevious($start, $end);

    expect($comparison['metrics']['total_violations']['current'])->toBe(2)
        ->and($comparison['metrics']['total_violations']['previous'])->toBe(1)
        ->and($comparison['metrics']['total_violations']['absolute'])->toBe(1.0);
});

it('forecasts incidents using regression', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    for ($m = 11; $m >= 0; $m--) {
        $date = Carbon::now()->subMonths($m)->startOfMonth()->addDays(14);
        createViolation($citizen, $officer, $date, 10000);
    }

    $forecast = app(AnalyticsService::class)->forecastIncidents(12, 3);

    expect($forecast['history'])->toHaveCount(12)
        ->and($forecast['forecast'])->toHaveCount(3);
});

it('builds custom reports by type and status', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    createReport($citizen, ReportStatus::Resolved->value, 'دمشق', Carbon::create(2026, 3, 1), Carbon::create(2026, 3, 1));
    createViolation($citizen, $officer, Carbon::create(2026, 3, 5, 9), 30000, 'unpaid');

    $service = app(AnalyticsService::class);

    $reports = $service->customReport(['type' => 'reports', 'from' => '2026-01-01', 'to' => '2026-12-31']);
    $violations = $service->customReport(['type' => 'violations', 'from' => '2026-01-01', 'to' => '2026-12-31', 'status' => ViolationStatus::Unpaid->value]);

    expect($reports)->toHaveCount(1)
        ->and($reports[0]['type'])->toBe('report')
        ->and($violations)->toHaveCount(1)
        ->and($violations[0]['fine'])->toBe(30000.0);
});

it('rejects disallowed distribution columns', function () {
    $service = app(AnalyticsService::class);
    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 6, 30);

    expect(fn () => $service->reportDistribution($start, $end, 'password; DROP TABLE reports; --'))
        ->toThrow(\InvalidArgumentException::class)
        ->and(fn () => $service->violationDistribution($start, $end, 'evil_column'))
        ->toThrow(\InvalidArgumentException::class);
});

it('excludes canceled fines from collection rate and includes pending in outstanding', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 6, 30);

    createViolation($citizen, $officer, Carbon::create(2026, 2, 1, 9), 50000, 'unpaid', 'دمشق');
    createViolation($citizen, $officer, Carbon::create(2026, 3, 1, 9), 25000, 'paid', 'حلب');
    createViolation($citizen, $officer, Carbon::create(2026, 4, 1, 9), 30000, 'pending_verification', 'حلب');
    createViolation($citizen, $officer, Carbon::create(2026, 5, 1, 9), 10000, 'canceled', 'دمشق');

    $kpis = app(AnalyticsService::class)->kpis($start, $end);

    // paid=1, actionable (excluding canceled)=3 -> 33.33%
    expect($kpis['collection_rate'])->toBe(33.33)
        ->and($kpis['outstanding_fines'])->toBe(80000.0); // unpaid 50000 + pending 30000
});

it('counts report hours in hotspots peak hours', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    $start = Carbon::create(2026, 1, 1);
    $end = Carbon::create(2026, 6, 30);

    createReport($citizen, ReportStatus::New->value, 'دمشق', Carbon::create(2026, 3, 1, 14), Carbon::create(2026, 3, 1, 14));
    createReport($citizen, ReportStatus::New->value, 'دمشق', Carbon::create(2026, 3, 2, 14), Carbon::create(2026, 3, 2, 14));
    createViolation($citizen, $officer, Carbon::create(2026, 3, 3, 14), 10000, 'unpaid', 'دمشق');

    $hotspots = app(AnalyticsService::class)->hotspots($start, $end);

    expect($hotspots['peak_hours'][14])->toBe(3);
});

it('derives forecast trend direction from regression slope', function () {
    [$user, $citizen] = makeCitizen();
    $officer = makeOfficer();

    // Increasing monthly counts: 1, 2, 3 -> positive slope -> up
    createViolation($citizen, $officer, Carbon::now()->subMonths(2)->startOfMonth()->addDays(5), 10000);
    createViolation($citizen, $officer, Carbon::now()->subMonths(1)->startOfMonth()->addDays(5), 10000);
    createViolation($citizen, $officer, Carbon::now()->subMonths(1)->startOfMonth()->addDays(6), 10000);
    createViolation($citizen, $officer, Carbon::now()->startOfMonth()->addDays(5), 10000);
    createViolation($citizen, $officer, Carbon::now()->startOfMonth()->addDays(6), 10000);
    createViolation($citizen, $officer, Carbon::now()->startOfMonth()->addDays(7), 10000);

    $forecast = app(AnalyticsService::class)->forecastIncidents(3, 3);

    expect($forecast['trend'])->toBe('up');
});
