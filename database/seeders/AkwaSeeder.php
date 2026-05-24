<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Candidat;
use App\Models\Parametre;
use App\Models\Talent;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkwaSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['email' => 'admin@akwaba.local'],
            ['name' => 'Administrateur', 'password' => Hash::make('password')]
        );

        Parametre::query()->updateOrCreate(['id' => 1], [
            'nom_evenement' => 'AKWABA STIC 25',
            'message_accueil' => 'Bienvenue ! Votez pour vos talents préférés de la soirée STIC.',
            'date_debut_vote' => now()->subDay(),
            'date_fin_vote' => now()->addDays(7),
            'votes_ouverts' => true,
        ]);

        $talentsData = [
            ['nom' => 'Meilleur Danseur', 'ordre' => 1],
            ['nom' => 'Filleul le plus élégant', 'ordre' => 2],
            ['nom' => 'Meilleur Performer', 'ordre' => 3],
        ];

        $noms = [
            ['Kofi Mensah', 'Ama Diallo', 'Jean-Paul N\'Guessan'],
            ['Sarah Koné', 'Ibrahim Touré', 'Grace Ahou'],
            ['Michel Bamba', 'Fatou Soro', 'David Kacou'],
        ];

        foreach ($talentsData as $i => $data) {
            $talent = Talent::query()->updateOrCreate(
                ['nom' => $data['nom']],
                ['votes_actifs' => true, 'ordre' => $data['ordre']]
            );

            foreach ($noms[$i] as $nom) {
                Candidat::query()->firstOrCreate(
                    ['talent_id' => $talent->id, 'nom_complet' => $nom]
                );
            }
        }

        $session = 'demo-seed-session';

        foreach (Candidat::with('talent')->get() as $index => $candidat) {
            if ($index % 2 === 0) {
                Vote::query()->create([
                    'talent_id' => $candidat->talent_id,
                    'candidat_id' => $candidat->id,
                    'session_id' => $session.'-'.$candidat->talent_id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Seeder',
                    'is_valid' => true,
                    'created_at' => now()->subMinutes(rand(1, 120)),
                ]);
            }
        }
    }
}
