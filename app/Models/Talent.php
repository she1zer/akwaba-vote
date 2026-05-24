<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Talent extends Model
{
    use HasFactory;

    protected $table = 'talents';

    protected $fillable = ['nom', 'icone_svg', 'votes_actifs', 'ordre'];

    protected function casts(): array
    {
        return [
            'votes_actifs' => 'boolean',
        ];
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(Candidat::class)->orderBy('nom_complet');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function validVotesCount(): int
    {
        return $this->votes()->where('is_valid', true)->count();
    }
}
