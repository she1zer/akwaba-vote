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
            'nom_evenement'           => 'AKWABA STIC 25',
            'message_accueil'         => 'Bienvenue ! Votez pour vos talents préférés de la soirée STIC.',
            'date_debut_vote'         => now()->subDay(),
            'date_fin_vote'           => now()->addDays(7),
            'votes_ouverts'           => true,
            'afficher_resultats_live' => true,
            'afficher_nb_votes'       => true,
            'couleur_primaire'        => '#cc0000',
            'couleur_secondaire'      => '#b8960c',
        ]);

        $talentsData = [
            ['nom' => 'Meilleur Danseur', 'description' => 'La star de la piste de danse', 'ordre' => 1, 'couleur_hex' => '#cc0000'],
            ['nom' => 'Filleul le plus élégant', 'description' => 'Style et classe au rendez-vous', 'ordre' => 2, 'couleur_hex' => '#b8960c'],
            ['nom' => 'Meilleur Performer', 'description' => 'Scène, charisme et performance', 'ordre' => 3, 'couleur_hex' => '#6d28d9'],
        ];

        $noms = [
            [
                ['nom' => 'Kofi Mensah',        'slogan' => 'Le groove est en moi',      'genre' => 'M'],
                ['nom' => 'Ama Diallo',          'slogan' => 'La danse, c\'est ma vie',   'genre' => 'F'],
                ['nom' => "Jean-Paul N'Guessan", 'slogan' => 'Chaque pas est un message', 'genre' => 'M'],
            ],
            [
                ['nom' => 'Sarah Koné',   'slogan' => 'L\'élégance sans effort', 'genre' => 'F'],
                ['nom' => 'Ibrahim Touré','slogan' => 'Style africain moderne',   'genre' => 'M'],
                ['nom' => 'Grace Ahou',   'slogan' => 'La classe incarnée',       'genre' => 'F'],
            ],
            [
                ['nom' => 'Michel Bamba', 'slogan' => 'La scène m\'appartient',  'genre' => 'M'],
                ['nom' => 'Fatou Soro',   'slogan' => 'Performance et passion',  'genre' => 'F'],
                ['nom' => 'David Kacou',  'slogan' => 'L\'art de se surpasser',  'genre' => 'M'],
            ],
        ];

        foreach ($talentsData as $i => $data) {
            $talent = Talent::query()->updateOrCreate(
                ['nom' => $data['nom']],
                array_merge($data, ['votes_actifs' => true, 'max_votes_par_ip' => 1])
            );

            foreach ($noms[$i] as $ordre => $candidatData) {
                Candidat::query()->firstOrCreate(
                    ['talent_id' => $talent->id, 'nom_complet' => $candidatData['nom']],
                    ['slogan' => $candidatData['slogan'], 'genre' => $candidatData['genre'], 'ordre' => $ordre, 'is_active' => true]
                );
            }
        }

        $session = 'demo-seed-session';
        foreach (Candidat::with('talent')->get() as $index => $candidat) {
            if ($index % 2 === 0) {
                Vote::query()->create([
                    'talent_id'       => $candidat->talent_id,
                    'candidat_id'     => $candidat->id,
                    'session_id'      => $session.'-'.$candidat->talent_id,
                    'ip_address'      => '127.0.0.1',
                    'user_agent'      => 'Seeder',
                    'score_confiance' => 100,
                    'is_valid'        => true,
                    'is_flagged'      => false,
                    'created_at'      => now()->subMinutes(rand(1, 120)),
                ]);
            }
        }
    }
}
