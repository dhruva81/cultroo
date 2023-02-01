<?php

use App\Livewire\Genres\GenreCreateOrUpdate;
use App\Livewire\Genres\GenresDatatable;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

// Datatables Test
it('ensures that guests cannot access genres datatable page')
    ->get('/admin/genres')
    ->assertStatus(302)
    ->assertRedirect('/login');

test('genres datatable page has a genres datatable component')
    ->livewire(GenresDatatable::class)
    ->assertSuccessful()
    ->assertFormExists();

test('genres datatable can list genres', function () {
    $genres = Genre::factory()->count(10)->create();

    livewire(GenresDatatable::class)
        ->assertCanSeeTableRecords($genres);
});

test('genres datatable has name, icon, cover_image and status', function () {
    Genre::factory()->count(10)->create();

    livewire(GenresDatatable::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('icon')
        ->assertCanRenderTableColumn('cover_image')
        ->assertCanRenderTableColumn('status');

});

test('genres datatable can search by name', function () {
    $genres = Genre::factory()->count(10)->create();

    $name = $genres->first()->name;

    livewire(GenresDatatable::class)
        ->searchTable($name)
        ->assertCanSeeTableRecords($genres->where('name', $name))
        ->assertCanNotSeeTableRecords($genres->where('name', '!=', $name));
});


