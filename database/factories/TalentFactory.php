<?php

namespace Database\Factories;

use App\Models\Talent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Talent> */
class TalentFactory extends Factory
{
    protected $model = Talent::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->words(3, true),
            'votes_actifs' => true,
            'ordre' => fake()->numberBetween(0, 10),
        ];
    }
}
