<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'nom_evenement',
        'message_accueil',
        'date_debut_vote',
        'date_fin_vote',
        'votes_ouverts',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_vote' => 'datetime',
            'date_fin_vote' => 'datetime',
            'votes_ouverts' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nom_evenement' => 'AKWABA STIC 25',
            'message_accueil' => 'Bienvenue sur la plateforme de vote officielle.',
            'votes_ouverts' => true,
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
}
