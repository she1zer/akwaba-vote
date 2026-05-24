<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Candidat extends Model
{
    use HasFactory;

    protected $table = 'candidats';

    protected $fillable = ['talent_id', 'nom_complet', 'photo', 'photo_thumb'];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function validVotesCount(): int
    {
        return $this->votes()->where('is_valid', true)->count();
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }

    public function thumbUrl(): ?string
    {
        if ($this->photo_thumb) {
            return Storage::disk('public')->url($this->photo_thumb);
        }

        return $this->photoUrl();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->nom_complet)) ?: [];

        return Str::upper(collect($parts)->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->implode(''));
    }
}
