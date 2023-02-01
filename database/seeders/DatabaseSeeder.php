<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            LanguageSeeder::class,
            CharacterSeeder::class,
            GenreSeeder::class,
            TagSeeder::class,
            KollectionSeeder::class,
            ShortsSeeder::class,
            SeriesSeeder::class,
//            PlanSeeder::class,
        ]);
    }
}
