<?php

use App\Models\AdminData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminUser(): User
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

it('blocks analytics pages for guests', function () {
    $this->get('/admin/advanced-analytics')->assertRedirect('/login');
    $this->get('/admin/custom-report-builder')->assertRedirect('/login');
});

it('renders the advanced analytics page for admins', function () {
    $this->withoutExceptionHandling();
    $this->actingAs(adminUser())
        ->get('/admin/advanced-analytics')
        ->assertSuccessful();
});

it('renders the custom report builder page for admins', function () {
    $this->withoutExceptionHandling();
    $this->actingAs(adminUser())
        ->get('/admin/custom-report-builder')
        ->assertSuccessful();
});
