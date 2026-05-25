<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Talent extends Model
{
    use HasFactory;

    protected $table = 'talents';

    protected $fillable = [
        'nom', 'description', 'icone_svg', 'votes_actifs',
        'ordre', 'couleur_hex', 'max_votes_par_ip',
        'allow_candidature_spontanee',
    ];

    protected function casts(): array
    {
        return [
            'votes_actifs'                => 'boolean',
            'max_votes_par_ip'            => 'integer',
            'allow_candidature_spontanee' => 'boolean',
        ];
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(Candidat::class)->orderBy('ordre')->orderBy('nom_complet');
    }

    // Candidats validés et actifs — utilisé pour le vote public
    public function candidatsActifs(): HasMany
    {
        return $this->hasMany(Candidat::class)
            ->where('statut', 'valide')
            ->where('is_active', true)
            ->orderBy('ordre')
            ->orderBy('nom_complet');
    }

    // Candidats en attente de validation admin
    public function candidatsEnAttente(): HasMany
    {
        return $this->hasMany(Candidat::class)
            ->where('statut', 'en_attente')
            ->orderBy('propose_le');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function validVotesCount(): int
    {
        return $this->votes()->where('is_valid', true)->count();
    }

    public function votesParHeure(): array
    {
        return $this->votes()
            ->where('is_valid', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('HOUR(created_at) as heure, COUNT(*) as total')
            ->groupBy('heure')
            ->pluck('total', 'heure')
            ->toArray();
    }
}
