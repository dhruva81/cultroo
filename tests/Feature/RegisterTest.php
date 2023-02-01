<?php

use App\Livewire\Users\RegisterAdminMember;
use App\Models\PendingUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

//test('registration screen can be rendered', function () {
//    if (! Features::enabled(Features::registration())) {
//        return $this->markTestSkipped('Default Registration support is disabled.');
//    }
//
//    $response = $this->get('/admin/register');
//
//    $response->assertStatus(200);
//});
//
//it('can see register admin member component', function () {
//    $this->get('/admin/register')
//        ->assertSeeLivewire(RegisterAdminMember::class);
//});
//
//test('default register screen cannot be rendered if support is disabled', function () {
//    if (Features::enabled(Features::registration())) {
//        return $this->markTestSkipped('Fortify Default Registration support is enabled.');
//    }
//
//    $response = $this->get('/register');
//
//    $response->assertStatus(404);
//});
//
//it('can send verification otp required for registration', function () {
//    $this->assertTrue(true);
////    $pendingUser = PendingUser::create([
////        'name' => 'Pending User',
////        'email' => 'pendingUser@gmail.com',
////        'otp' => 1234,
////        'user_type' => 'admin_user'
////    ]);
////
////    $this->assertDatabaseHas('pending_users', [
////        'email' => 'pendingUser@gmail.com',
////        'user_type' => 'admin_user',
////    ]);
////
////    livewire(RegisterAdminMember::class)
////        ->fillForm([
////            'email' => 'pendingUser@gmail.com',
////            'otp' => 1234,
////            'password' => 'password',
////            'password_confirmation' => 'password',
////        ])
////        ->call('submit')
////        ->assertHasNoFormErrors();
//
////    $res = Livewire::test(RegisterAdminMember::class)
////        ->set('email', 'pendingUser@gmail.com')
////        ->set('otp', 1234)
////        ->set('password', 'password')
////        ->set('password_confirmation', 'password')
////        ->call('submit');
////
////    $res->assertHasErrors();
//
////    $res->dd();
//
////    $this->assertRedirect('/admin/dashboard');
//
////    $this->assertTrue(User::whereEmail('pendingUser@gmail.com')->exists());
//
////    $this->assertDatabaseHas('pending_users', [
////        'email' => 'pendingUser@gmail.com',
////        'user_type' => 'admin_user',
////    ]);
//});
