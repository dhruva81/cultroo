<?php

use App\Livewire\Languages\LanguagesDatatable;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('ensures that guests cannot access languages datatable page')
    ->get('/admin/languages')
    ->assertStatus(302)
    ->assertRedirect('/login');

test('languages datatable page has a languages datatable component')
    ->livewire(LanguagesDatatable::class)
    ->assertSuccessful()
    ->assertFormExists();

test('languages datatable can list languages', function () {
    $languages = Language::factory()->count(10)->create();

    livewire(LanguagesDatatable::class)
        ->assertCanSeeTableRecords($languages);
});

test('languages datatable has name and status', function () {
    Language::factory()->count(10)->create();

    livewire(LanguagesDatatable::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('status');

});

test('languages datatable can search by name', function () {
    $languages = Language::factory()->count(10)->create();

    $name = $languages->first()->name;

    livewire(LanguagesDatatable::class)
        ->searchTable($name)
        ->assertCanSeeTableRecords($languages->where('name', $name))
        ->assertCanNotSeeTableRecords($languages->where('name', '!=', $name));
});



