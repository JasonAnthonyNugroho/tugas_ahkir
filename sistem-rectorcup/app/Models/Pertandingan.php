<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertandingan extends Model
{
    protected $fillable = [
        'team_a_id',
        'team_b_id',
        'score_a',
        'score_b',
        'waktu_tanding',
        'lokasi',
        'status',
        'selesai_pada',
    ];

    protected $casts = [
        'waktu_tanding' => 'datetime',
        'selesai_pada' => 'datetime',
    ];

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }
}
