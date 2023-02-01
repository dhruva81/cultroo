<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Series;
use App\Models\Tag;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $series = [
            'Diwali - Festival of lights',
            'Holi - Festival of colors',
            'Raksha Bandhan',
            'Ganesh Chaturthi',
            'Navratri',
            'The Ramayana',
            'The Mahabharata',
            'The Tales of Panchatantra',
            'The Tales of Akbar and Birbal',
            'The Tales of Tenali Raman',
            'The Tales of Krishna'
        ];

        collect($series)
            ->each(function ($series) {
                Series::firstOrCreate(['title' => $series],
                    [
                    'status' => Arr::random([1, 2, 3]),
                    'description' => fake()->paragraph(),
                    'synopsis' => fake()->paragraph(),
                ]);
            });

        Series::all()
            ->each(function ($series) {

                // Add characters, genres, tags, kollections
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


                // Create Episodes
                Video::factory(random_int(5, 10))
                    ->create(['series_id' => $series->id]);

                $groupedVideos = Video::where('series_id', $series->id)
                    ->get()
                    ->groupBy('language_id');

                foreach($groupedVideos as $language => $videos) {
                    $videos->each(function ($video, $key) use ($videos) {
                        $video->update([
                            'episode_number' => $key + 1,
                        ]);
                    });
                }
            });

    }
}


