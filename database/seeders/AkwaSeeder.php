<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Parametre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkwaSeeder extends Seeder
{
    public function run(): void
    {
        // Compte admin par défaut
        Admin::query()->updateOrCreate(
            ['email' => 'admin@akwaba.local'],
            ['name' => 'Administrateur', 'password' => Hash::make('password')]
        );

        // Paramètres de base de l'événement
        Parametre::query()->updateOrCreate(['id' => 1], [
            'nom_evenement'           => 'AKWABA STIC 25',
            'message_accueil'         => 'Bienvenue sur la plateforme de vote officielle AKWABA STIC 25.',
            'votes_ouverts'           => true,
            'afficher_resultats_live' => true,
            'afficher_nb_votes'       => true,
            'couleur_primaire'        => '#cc0000',
            'couleur_secondaire'      => '#b8960c',
        ]);
    }
}
