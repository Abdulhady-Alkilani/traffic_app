<?php

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Models\AdminData;
use App\Models\CitizenData;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $this->admin = User::create([
        'username' => 'admin',
        'email' => 'admin@test.com',
        'password' => 'password',
        'role_id' => $this->role->id,
        'is_active' => true,
    ]);
    AdminData::create(['user_id' => $this->admin->id, 'full_name' => 'Site Admin']);
});

it('renders the admin reports list page', function () {
    $this->actingAs($this->admin)
        ->get('/admin/reports')
        ->assertSuccessful();
});

it('renders the admin report create page with form fields', function () {
    $response = $this->actingAs($this->admin)
        ->get('/admin/reports/create');

    $response->assertSuccessful();
    $response->assertSee('latitude');
    $response->assertSee('longitude');
    $response->assertSee('location_text');
});

it('renders the admin report edit page with form fields and map', function () {
    $citizen = CitizenData::create([
        'user_id' => $this->admin->id,
        'full_name' => 'Citizen Test',
        'national_id' => '1234567890',
        'phone' => '0500000000',
        'blood_type' => 'O+',
    ]);

    $report = Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::HighwayPatrol->value,
        'report_type' => 'accident',
        'description' => 'Test report description',
        'latitude' => 35.13,
        'longitude' => 36.75,
        'location_text' => 'Test Location',
        'status' => ReportStatus::New,
    ]);

    $response = $this->actingAs($this->admin)
        ->get("/admin/reports/{$report->id}/edit");

    $response->assertSuccessful();
    $response->assertSee('latitude');
    $response->assertSee('longitude');
});

it('renders the admin report view page', function () {
    $citizen = CitizenData::create([
        'user_id' => $this->admin->id,
        'full_name' => 'Citizen Test',
        'national_id' => '1234567890',
        'phone' => '0500000000',
        'blood_type' => 'O+',
    ]);

    $report = Report::create([
        'citizen_id' => $citizen->id,
        'assigned_department' => Department::HighwayPatrol->value,
        'report_type' => 'accident',
        'description' => 'Test report description',
        'latitude' => 35.13,
        'longitude' => 36.75,
        'location_text' => 'Test Location',
        'status' => ReportStatus::New,
    ]);

    $this->actingAs($this->admin)
        ->get("/admin/reports/{$report->id}")
        ->assertSuccessful();
});
