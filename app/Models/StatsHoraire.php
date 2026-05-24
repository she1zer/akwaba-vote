<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatsHoraire extends Model
{
    public $timestamps = false;

    protected $table = 'stats_horaires';

    protected $fillable = [
        'talent_id',
        'candidat_id',
        'heure',
        'date_stat',
        'nb_votes',
    ];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }
}
