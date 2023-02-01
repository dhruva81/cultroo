<?php

namespace App\Imports;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Series;
use App\Models\Video;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Tags\Tag;

class DataImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $characters = isset($row['characters']) && ($row['characters'] != null) ? $row['characters'] : null;
            $languages = isset($row['languages']) && ($row['languages'] != null) ? $row['languages'] : null;
            $status = isset($row['status']) && ($row['status'] != null) ? $row['status'] : null;

            $series_title = isset($row['series_title']) && ($row['series_title'] != null) ? $row['series_title'] : null;
            $series_description = isset($row['series_description']) && ($row['series_description'] != null) ? $row['series_description'] : null;

            $episode_title = isset($row['episode_title']) && ($row['episode_title'] != null) ? $row['episode_title'] : null;
            $episode_description = isset($row['episode_description']) && ($row['episode_description'] != null) ? $row['episode_description'] : null;
            $run_time = isset($row['run_time']) && ($row['run_time'] != null) ? $row['run_time'] : null;

            $min_age = isset($row['min_age']) && ($row['min_age'] != null) ? str_replace('+', '', $row['min_age']) : null;

            // Tags
            $tags = isset($row['tags']) && ($row['tags'] != null) ? $row['tags'] : null;

            //Genres
            $genres = isset($row['genre']) && ($row['genre'] != null) ? $row['genre'] : null;

            if ($series_title) {
                $series = Series::firstOrCreate(['title' => $series_title], [
                    'status' => 1,
                    'min_age' => (int) $min_age,
                ]);

                if ($episode_title) {
                    $video = Video::firstOrCreate(['title' => $episode_title], [
                        'min_age' => (int) $min_age,
                        'run_time' => $this->convertTimeToSecond($run_time),
                        'status' => $this->getStatus($status),
                        'series_id' => $series->id,
                    ]);
                }

                if ($characters) {
                    $explodedCharacters = explode(',', $characters);

                    foreach ($explodedCharacters as $character) {
                        $theCharacter = Character::firstOrCreate(['name' => $character]);
                        $video->characters()->syncWithoutDetaching($theCharacter->id);
                    }
                }

                if ($genres) {
                    $explodedGenres = explode(',', $genres);

                    foreach ($explodedGenres as $genre) {
                        $theGenre = Genre::firstOrCreate(['name' => $genre]);
                        $video->genres()->syncWithoutDetaching($theGenre->id);
                    }
                }

                if ($tags) {
                    $explodedTags = explode(',', $tags);

                    foreach ($explodedTags as $tag) {
//                        $theTag = Tag::firstOrCreate(['name' => trim($tag)], [
//                            'slug' => Str::slug($tag)
//                        ]);
                        $video->attachTag($tag);
                        $series->attachTag($tag);
                    }
                }
            }
        }
    }

    protected function convertTimeToSecond($time = null)
    {
        if (! $time) {
            return null;
        }

        if (count(explode(',', $time)) > 1) {
            return null;
        }

        $d = explode(':', $time);

        $units = isset($d[1]) ? $d[1] : 0;
        $tens = isset($d[0]) ? $d[0] * 60 : 0;

        return $tens + $units;
    }

    protected function getStatus($status = null)
    {
        if (! $status) {
            return null;
        }

        if ($status === 'Done' || $status === 'done') {
            return 1;
        }

        return 2;
    }
}
