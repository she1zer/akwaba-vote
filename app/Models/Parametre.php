<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'nom_evenement', 'message_accueil',
        'date_debut_vote', 'date_fin_vote', 'votes_ouverts',
        'logo', 'afficher_resultats_live', 'afficher_nb_votes',
        'couleur_primaire', 'couleur_secondaire',
        'lien_facebook', 'lien_instagram', 'reglement',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_vote' => 'datetime',
            'date_fin_vote' => 'datetime',
            'votes_ouverts' => 'boolean',
            'afficher_resultats_live' => 'boolean',
            'afficher_nb_votes' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nom_evenement' => 'AKWABA STIC 25',
            'message_accueil' => 'Bienvenue sur la plateforme de vote officielle.',
            'votes_ouverts' => true,
            'afficher_resultats_live' => true,
            'afficher_nb_votes' => true,
            'couleur_primaire' => '#cc0000',
            'couleur_secondaire' => '#b8960c',
        ]);
    }

    public function votesAreOpen(): bool
    {
        if (! $this->votes_ouverts) {
            return false;
        }
        $now = now();
        if ($this->date_debut_vote && $now->lt($this->date_debut_vote)) {
            return false;
        }
        if ($this->date_fin_vote && $now->gt($this->date_fin_vote)) {
            return false;
        }
        return true;
    }

    public function tempsRestant(): ?string
    {
        if (! $this->date_fin_vote || ! $this->votesAreOpen()) {
            return null;
        }
        return now()->diffForHumans($this->date_fin_vote, true);
    }
}
