<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'Action',
            'Adventure',
            'Comedy',
            'Drama',
            'Fantasy',
            'Historical',
            'Mystery',
            'Political',
            'Romance',
            'Saga',
            'Satire',
            'Science Fiction',
            'Social',
            'Thriller',
            'Urban',
            'Western',
            'Biography',
            'Business',
            'Cookbook',
            'Diary',
            'Dictionary',
            'Encyclopedia',
            'Guide',
            'Health',
            'History',
            'Journal',
            'Math',
            'Memoir',
            'Prayer',
            'Religion',
            'Review',
            'Textbook',
            'Travel',
            'Art',
            'Anthology',
            'Story',
        ];

        collect($tags)
            ->each(function ($tag) {
                Tag::firstOrCreate(['name' => $tag]);
            });

    }
}
