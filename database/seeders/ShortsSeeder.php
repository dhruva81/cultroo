<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Video;
use Illuminate\Database\Seeder;

class ShortsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Video::factory(15)->create();
    }
}
