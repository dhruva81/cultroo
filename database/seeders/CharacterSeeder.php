<?php

namespace Database\Seeders;

use App\Models\Character;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $characters = [
            'Krishna',
            'Rama',
            'Shiva',
            'Hanuman',
            'Ganesha',
            'Bhagwan',
            'Brahma',
            'Vishnu',
            'Mahesh',
            'Shakti',
            'Guru Ram Das',
            'Guru Gobind Singh',
            'Guru Granth Sahib',
            'Guru Nanak',
            'Guru Angad',
            'Guru Amar Das',
            'Guru Ram Das',
            'Guru Arjan',
            'Guru Har Rai',
            'Guru Har Krishan',
            'Guru Tegh Bahadur',
        ];

        collect($characters)
            ->each(function ($character) {
                Character::firstOrCreate(['name' => $character]);
            });
    }
}
