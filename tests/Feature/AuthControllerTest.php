<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('registration creates a user, starts a session, and sends a verification email', function () {
    Notification::fake();

    $this->postJson('/api/auth/register', [
        'name' => 'Pemilik Usaha',
        'email' => 'Pemilik@Example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ])->assertCreated()
        ->assertJsonPath('email', 'pemilik@example.com');

    $user = User::query()->where('email', 'pemilik@example.com')->firstOrFail();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email_verified_at' => null,
    ]);
    expect(Hash::check('secret-password', $user->password))->toBeTrue();
    $this->getJson('/api/auth/me')->assertOk()->assertJsonPath('id', $user->id);
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('registration rejects duplicate emails and short passwords with 422', function () {
    User::factory()->create(['email' => 'owner@example.com']);

    $this->postJson('/api/auth/register', [
        'name' => 'Pemilik Baru',
        'email' => 'OWNER@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);

    $this->assertDatabaseCount('users', 1);
});

test('login authenticates a user and accepts case-insensitive email', function () {
    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'password' => 'correct-password',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'OWNER@EXAMPLE.COM',
        'password' => 'correct-password',
    ])->assertOk()->assertJsonPath('id', $user->id);

    $this->getJson('/api/auth/me')->assertOk()->assertJsonPath('email', $user->email);
});

test('login returns 422 and does not authenticate when credentials are invalid', function () {
    User::factory()->create([
        'email' => 'owner@example.com',
        'password' => 'correct-password',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'owner@example.com',
        'password' => 'wrong-password',
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'Email atau kata sandi tidak valid.');

    $this->getJson('/api/auth/me')->assertUnauthorized();
});

test('logout invalidates the authenticated session', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/auth/logout')
        ->assertOk();

    $this->getJson('/api/auth/me')->assertUnauthorized();
});

test('unverified users receive 403 when accessing business endpoints', function () {
    $this->actingAs(User::factory()->unverified()->create())
        ->getJson('/api/business')
        ->assertForbidden();
});

test('a valid signed verification link verifies the authenticated user', function () {
    $user = User::factory()->unverified()->create();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(30),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ],
    );

    $this->actingAs($user)->getJson($verificationUrl)
        ->assertOk()
        ->assertJsonPath('message', 'Email berhasil diverifikasi.');

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('a forged verification link receives 403', function () {
    $user = User::factory()->unverified()->create();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(30),
        ['id' => $user->id, 'hash' => 'invalid-hash'],
    );

    $this->actingAs($user)->getJson($verificationUrl)->assertForbidden();

    $this->assertNull($user->fresh()->email_verified_at);
});

test('forgot password response does not reveal whether an email is registered', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'owner@example.com']);

    $unknownEmailResponse = $this->postJson('/api/auth/forgot-password', [
        'email' => 'unknown@example.com',
    ])->assertOk();
    $knownEmailResponse = $this->postJson('/api/auth/forgot-password', [
        'email' => $user->email,
    ])->assertOk();

    expect($knownEmailResponse->json('message'))->toBe($unknownEmailResponse->json('message'));
    Notification::assertSentTo($user, ResetPassword::class);

    $resetUrl = route('password.reset', [
        'token' => 'reset-token',
        'email' => $user->email,
    ]);

    $this->getJson($resetUrl)
        ->assertOk()
        ->assertJsonPath('token', 'reset-token')
        ->assertJsonPath('email', $user->email);
});

test('a valid password reset token updates the password', function () {
    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'password' => 'old-password',
    ]);
    $token = Password::createToken($user);

    $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertOk()
        ->assertJsonPath('message', 'Kata sandi berhasil diubah.');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('an invalid password reset token returns 422 without changing the password', function () {
    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'password' => 'old-password',
    ]);

    $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'token' => 'invalid-token',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertUnprocessable();

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});
