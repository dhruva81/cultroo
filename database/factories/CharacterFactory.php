<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'slug' => implode('-', $this->faker->words()),
            'short_description' => $this->faker->paragraph(),
            'description' => $this->faker->paragraph(3),
            'status' => Arr::random([1, 2, 3]),
            'created_by' => \App\Models\User::all()->random()?->id,
        ];
    }
}
