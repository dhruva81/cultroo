<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Series>
 */
class SeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => implode('-', $this->faker->words()),
            'description' => $this->faker->paragraph(3),
            'synopsis' => $this->faker->paragraph(3),
            'status' => Arr::random([1, 2, 3]),
            'created_by' => User::factory()->create()->id,
            'uuid' => Str::uuid(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Series $series) {
            $genres = Genre::all()->modelKeys();
            $tags  = Tag::all()->modelKeys();
            $collections = Kollection::all()->modelKeys();

            shuffle($genres);
            shuffle($tags);
            shuffle($collections);

            $newGenres = array_slice($genres, 0, 2);
            $newTags = array_slice($tags, 0, 3);
            $newCollections = array_slice($collections, 0, 3);

            $series->genres()->sync($newGenres);
            $series->tags()->sync($newTags);
            $series->collections()->sync($newCollections);
        });
    }
}
