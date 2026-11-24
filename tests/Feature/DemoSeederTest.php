<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates a verified demo account and business idempotently', function () {
    config([
        'demo.account.name' => 'Pemilik Demo',
        'demo.account.email' => 'demo@marginai.test',
        'demo.account.password' => 'demo-password',
        'demo.business.name' => 'Kedai Demo',
    ]);

    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('users', 1)
        ->assertDatabaseCount('businesses', 1);

    $this->postJson('/api/auth/login', [
        'email' => 'demo@marginai.test',
        'password' => 'demo-password',
    ])->assertOk()
        ->assertJsonPath('email', 'demo@marginai.test');

    $user = User::query()->where('email', 'demo@marginai.test')->firstOrFail();

    expect($user->email_verified_at)->not->toBeNull();
    expect($user->business->name)->toBe('Kedai Demo');

    $this->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('kpis.product_count', 0)
        ->assertJsonPath('kpis.average_material_cost_per_batch', null)
        ->assertJsonPath('trends.11.simulation_count', 0);
});

test('database seeder skips the demo account outside local and testing environments', function () {
    app()->instance('env', 'production');

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('users', 0)
        ->assertDatabaseCount('businesses', 0);
});
