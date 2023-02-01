<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('login screen can be rendered for guests', function () {
    $this
        ->get('/login')
        ->assertSuccessful()
        ->assertStatus(200)
        ->assertSee(['Email', 'Password', 'Remember me', 'Log in'])
        ->assertDontSee(['Verify your password?'])
        ->assertViewIs('auth.login');
});

test('guest can not access dashboard and redirected to login page', function () {
    $response = $this
        ->get('/admin/dashboard')
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('authenticated user can not see login page and redirected to dashboard', function () {
    $user = User::factory()->make();
    $response = $this->actingAs($user)->get('/login');
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users cannot login with incorrect password', function () {
    $user = User::factory()->create();

    $response = $this
        ->from('/login')
        ->post('/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertTrue(session()->hasOldInput('email'));
    $this->assertFalse(session()->hasOldInput('password'));
    $this->assertGuest();
});

test('remember me functionality is working', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertRedirect(RouteServiceProvider::HOME);

    $value = vsprintf('%s|%s|%s', [
        $user->id,
        $user->getRememberToken(),
        $user->password,
    ]);

    $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
        $user->id,
        $user->getRememberToken(),
        $user->password,
    ]));

    $this->assertAuthenticatedAs($user);
});
