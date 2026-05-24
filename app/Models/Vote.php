<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'talent_id',
        'candidat_id',
        'session_id',
        'ip_address',
        'user_agent',
        'is_valid',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_valid' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }
}
