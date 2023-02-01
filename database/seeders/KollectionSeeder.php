<?php

namespace Database\Seeders;

use App\Models\Kollection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class KollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kollections = [
            'Pohela Baisakh',
            'Bihu',
            'Camel Festival',
            'Durga Puja',
            'Gudhi Padwa',
            'Baisakhi',
        ];

        collect($kollections)
            ->each(function ($kollection) {
                Kollection::firstOrCreate(['name' => $kollection], [
                    'status' => Arr::random([1, 2, 3]),
                    'color' => Arr::random(['#FF0000', '#00FF00', '#0000FF']),
                ]);
            });
    }
}
