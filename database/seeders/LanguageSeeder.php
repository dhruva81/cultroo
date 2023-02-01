<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            'English',
            'Hindi',
            'Bengali',
            'Oriya',
            'Punjabi',
            'Haryanvi',
            'Telugu',
            'Tamil',
            'Marathi',
            'Bhojpuri',
        ];

        collect($languages)
            ->each(function ($language) {
                Language::firstOrCreate(['name' => $language], ['status' => Arr::random([1, 2]),]);
            });

    }
}
