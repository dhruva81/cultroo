<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

it('redirects to login page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertPathIs('/login')
            ->assertSee('Email');
    });
});

test('a user can login', function () {
    $user = User::factory()->create([
        'user_type' => 'super_admin',
    ]);
    $this->browse(function (Browser $browser) use ($user) {
        $browser
            ->visit('/')
            ->assertPathIs('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('button[type="submit"]')
            ->assertPathIs('/admin/dashboard')
            ->assertSee('Most Watched Videos')
            ->assertSee('Most Active Users')
            ->assertSee('Dashboard')
            ->click('#profileDropDown')
            ->click('#logout')
            ->assertPathIs('/login')
            ->assertSee('Email');
    });
});

test('authenticated user can see dashboard', function () {
    $user = User::factory()->create([
        'user_type' => 'super_admin',
    ]);
    $this->browse(function (Browser $browser) use ($user) {
        $browser
            ->loginAs($user)
            ->visit('/admin/dashboard')
            ->assertSee('Most Watched Videos')
            ->assertSee('Most Active Users')
            ->assertSee('Dashboard');
    });
});
