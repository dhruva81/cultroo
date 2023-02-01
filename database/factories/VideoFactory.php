<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $minAge = random_int(1, 12);

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => Arr::random([1, 2, 3]),
            'language_id' => Language::all()->random()?->id,
            'min_age' => $minAge,
            'created_by' => User::factory()->create()->id,
            'created_at' => \Carbon\Carbon::now()->addDays(rand(-60, 60)),
        ];
    }

    public function withSeries()
    {
        return $this->state(function (array $attributes) {
            return [
                'series_id' => Series::factory()->create()->id,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Video $video) {

            $video->status = Arr::random([1, 2, 3]);
            $video->save();

            $genres = Genre::all()->modelKeys();
            $characters = Character::all()->modelKeys();
            $tags  = Tag::all()->modelKeys();
            $collections = Kollection::all()->modelKeys();

            shuffle($genres);
            shuffle($characters);
            shuffle($tags);
            shuffle($collections);

            $newGenres = array_slice($genres, 0, 2);
            $newCharacters = array_slice($characters, 0, 3);
            $newTags = array_slice($tags, 0, 3);
            $newCollections = array_slice($collections, 0, 3);

            $video->genres()->sync($newGenres);
            $video->characters()->sync($newCharacters);
            $video->tags()->sync($newTags);
            $video->collections()->sync($newCollections);
        });
    }

}
