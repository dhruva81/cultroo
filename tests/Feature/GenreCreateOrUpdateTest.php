<?php

use App\Livewire\Genres\GenreCreateOrUpdate;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

// Create or Update Tests
it('ensures that guests cannot access genres create page')
    ->get('/admin/genres/create')
    ->assertStatus(302)
    ->assertRedirect('/login');

it('ensures that guests cannot access genres update page', function () {

    $genre = Genre::factory()->create();

    $this->get('/admin/genres/' . $genre->id . '/edit')
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('genres create and update page has a genres form')
    ->livewire(GenreCreateOrUpdate::class)
    ->assertFormExists();

test('genre form has a name, status and icon, cover_image fields')
    ->livewire(GenreCreateOrUpdate::class)
    ->assertFormFieldExists('name')
    ->assertFormFieldExists('status')
    ->assertFormFieldExists('icon')
    ->assertFormFieldExists('cover_image');

it('throws validate error if name and status fields are empty')
    ->livewire(GenreCreateOrUpdate::class)
    ->set('name', '')
    ->set('status', null)
    ->call('submit')
    ->assertHasErrors(['name' => 'required'])
    ->assertHasErrors(['status' => 'required']);

it('creates a genre', function () {

    livewire(GenreCreateOrUpdate::class)
        ->set('name', 'Test Genre')
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('genres', [
        'name' => 'Test Genre',
        'status' => 'draft',
    ]);
});

it('updates a genre', function () {

    $genre = Genre::factory()->create();

    livewire(GenreCreateOrUpdate::class, ['genre' => $genre])
        ->set('name', 'New Genre Name')
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('genres', [
        'name' => 'New Genre Name',
    ]);

});
