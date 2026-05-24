<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAdmin extends Model
{
    public $timestamps = false;

    protected $table = 'logs_admin';

    protected $fillable = ['admin_id', 'action', 'detail', 'ip_address', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
