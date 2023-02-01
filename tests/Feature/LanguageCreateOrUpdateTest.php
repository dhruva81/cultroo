<?php

use App\Livewire\Languages\LanguageCreateOrUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('ensures that guests cannot access languages create page')
    ->get('/admin/languages/create')
    ->assertStatus(302)
    ->assertRedirect('/login');

it('ensures that guests cannot access languages update page', function () {

    $language = \App\Models\Language::factory()->create();

    $this->get('/admin/languages/' . $language->id . '/edit')
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('languages create and update page has a language form')
    ->livewire(LanguageCreateOrUpdate::class)
    ->assertFormExists();

test('language form has a name and status fields')
    ->livewire(LanguageCreateOrUpdate::class)
    ->assertFormFieldExists('name')
    ->assertFormFieldExists('status');

it('throws validate error if name and status fields are empty')
    ->livewire(LanguageCreateOrUpdate::class)
    ->set('name', '')
    ->set('status', null)
    ->call('submit')
    ->assertHasErrors(['name' => 'required'])
    ->assertHasErrors(['status' => 'required']);

it('creates a language', function () {

    livewire(LanguageCreateOrUpdate::class)
        ->set('name', 'Test Language')
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('languages', [
        'name' => 'Test Language',
        'status' => 'draft',
    ]);
});

it('updates a language', function () {

    $language = \App\Models\Language::factory()->create();

    livewire(LanguageCreateOrUpdate::class, ['language' => $language])
        ->set('name', 'New Language Name')
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('languages', [
        'name' => 'New Language Name',
    ]);

});


