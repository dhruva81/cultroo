<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = [
            [
                'name' => 'Festival',
                'slug' => 'festival',
            ],
            [
                'name' => 'Faith',
                'slug' => 'faith',
            ],
            [
                'name' => 'Fables',
                'slug' => 'fables',
            ],
            [
                'name' => 'Moral Stories',
                'slug' => 'moral-stories',
            ],
            [
                'name' => 'Folk Tales',
                'slug' => 'folk-tales',
            ],
        ];

        collect($genres)
            ->each(function ($genre) {
                Genre::firstOrCreate($genre, ['status' => Arr::random([1, 2, 3])]);
            });
    }
}
