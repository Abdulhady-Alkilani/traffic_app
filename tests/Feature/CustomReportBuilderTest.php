<?php

use App\Enums\ReportStatus;
use App\Enums\ViolationStatus;
use App\Filament\Admin\Pages\CustomReportBuilder;
use App\Models\AdminData;
use App\Models\CitizenData;
use App\Models\PoliceData;
use App\Models\Report;
use App\Models\Role;
use App\Models\TrafficViolation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminUserCr(): User
{
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $user = User::create([
        'username' => 'admin',
        'email' => 'admin@test.com',
        'password' => 'password',
        'role_id' => $role->id,
        'is_active' => true,
    ]);
    AdminData::create(['user_id' => $user->id, 'full_name' => 'Site Admin']);

    return $user;
}

function makeCitizenCr(string $name, int $n): CitizenData
{
    $user = User::create([
        'username' => strtolower(str_replace(' ', '', $name)).$n,
        'email' => strtolower(str_replace(' ', '', $name)).$n.'@test.com',
        'password' => 'password',
    ]);

    return CitizenData::create([
        'user_id' => $user->id,
        'national_id' => '0000'.$n,
        'full_name' => $name,
        'phone' => '099'.$n,
        'blood_type' => 'O+',
    ]);
}

it('builds a reports custom report', function () {
    $admin = adminUserCr();
    $citizen = makeCitizenCr('Citizen One', 1);

    Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => 'local_police',
        'report_type' => 'accident',
        'description' => 'Test report',
        'location_text' => 'دمشق',
        'status' => ReportStatus::Resolved->value,
    ]);

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->set('reportType', 'reports')
        ->set('from', '2026-01-01')
        ->set('to', '2026-12-31')
        ->call('build')
        ->assertHasNoErrors()
        ->assertSet('built', true)
        ->assertSee('Citizen One');
});

it('builds a violations custom report', function () {
    $admin = adminUserCr();
    $citizen = makeCitizenCr('Citizen Two', 2);
    $officer = PoliceData::create([
        'user_id' => User::create([
            'username' => 'officer1',
            'email' => 'officer1@test.com',
            'password' => 'password',
        ])->id,
        'badge_number' => 'B1000',
        'full_name' => 'Officer Test',
        'rank' => 'Sergeant',
        'department' => 'traffic_police',
    ]);

    TrafficViolation::create([
        'citizen_id' => $citizen->id,
        'police_id' => $officer->id,
        'violation_type' => 'speeding',
        'description' => 'دمشق',
        'fine_amount' => 50000,
        'status' => ViolationStatus::Unpaid->value,
        'issued_at' => '2026-03-01 10:00:00',
        'due_date' => '2026-04-01',
    ]);

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->set('reportType', 'violations')
        ->set('from', '2026-01-01')
        ->set('to', '2026-12-31')
        ->call('build')
        ->assertHasNoErrors()
        ->assertSet('built', true)
        ->assertSee('Citizen Two');
});

it('builds an incidents custom report', function () {
    $admin = adminUserCr();
    $citizen = makeCitizenCr('Citizen Three', 3);
    $officer = PoliceData::create([
        'user_id' => User::create([
            'username' => 'officer2',
            'email' => 'officer2@test.com',
            'password' => 'password',
        ])->id,
        'badge_number' => 'B2000',
        'full_name' => 'Officer Two',
        'rank' => 'Sergeant',
        'department' => 'traffic_police',
    ]);

    Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => 'local_police',
        'report_type' => 'accident',
        'description' => 'Incident report',
        'location_text' => 'حلب',
        'status' => ReportStatus::New->value,
    ]);

    TrafficViolation::create([
        'citizen_id' => $citizen->id,
        'police_id' => $officer->id,
        'violation_type' => 'speeding',
        'description' => 'حلب',
        'fine_amount' => 25000,
        'status' => ViolationStatus::Paid->value,
        'issued_at' => '2026-04-01 10:00:00',
        'due_date' => '2026-05-01',
    ]);

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->set('reportType', 'incidents')
        ->set('from', '2026-01-01')
        ->set('to', '2026-12-31')
        ->call('build')
        ->assertHasNoErrors()
        ->assertSet('built', true)
        ->assertSee('Citizen Three');
});

it('builds with the default filters without setting anything', function () {
    $admin = adminUserCr();
    $citizen = makeCitizenCr('Citizen Four', 4);

    Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => 'local_police',
        'report_type' => 'accident',
        'description' => 'Default report',
        'location_text' => 'حمص',
        'status' => ReportStatus::New->value,
    ]);

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->call('build')
        ->assertHasNoErrors()
        ->assertSet('built', true);
});

it('resets filters', function () {
    $admin = adminUserCr();

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->set('reportType', 'violations')
        ->set('statusFilter', 'unpaid')
        ->set('from', '2020-01-01')
        ->set('to', '2020-12-31')
        ->call('resetFilters')
        ->assertSet('reportType', 'reports')
        ->assertSet('statusFilter', null)
        ->assertSet('built', false)
        ->assertSet('results', []);
});

it('validates the date range', function () {
    $admin = adminUserCr();

    $this->actingAs($admin);

    Livewire::test(CustomReportBuilder::class)
        ->set('from', '2026-12-31')
        ->set('to', '2026-01-01')
        ->call('build')
        ->assertHasErrors(['to']);
});
