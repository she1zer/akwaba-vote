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
    ];

    protected function casts(): array
    {
        return [
            'votes_actifs' => 'boolean',
            'max_votes_par_ip' => 'integer',
        ];
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(Candidat::class)->orderBy('ordre')->orderBy('nom_complet');
    }

    public function candidatsActifs(): HasMany
    {
        return $this->hasMany(Candidat::class)->where('is_active', true)->orderBy('ordre')->orderBy('nom_complet');
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
