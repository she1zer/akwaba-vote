<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionVote extends Model
{
    protected $table = 'sessions_vote';

    protected $fillable = ['token', 'label', 'is_active', 'nb_votes_emis'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function generate(string $label = ''): self
    {
        return static::create([
            'token' => Str::random(64),
            'label' => $label,
            'is_active' => true,
        ]);
    }
}
