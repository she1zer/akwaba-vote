<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'candidat_id',
        'session_id',
        'ip_address',
        'type',
        'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }
}
