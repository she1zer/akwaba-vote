<?php

namespace Database\Factories;

use App\Models\Candidat;
use App\Models\Talent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Candidat> */
class CandidatFactory extends Factory
{
    protected $model = Candidat::class;

    public function definition(): array
    {
        return [
            'talent_id' => Talent::factory(),
            'nom_complet' => fake()->name(),
        ];
    }
}
