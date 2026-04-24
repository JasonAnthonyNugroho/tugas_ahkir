<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertandingan extends Model
{
    protected $fillable = [
        'team_a_id',
        'team_b_id',
        'score_a',
        'score_b',
        'waktu_tanding',
        'lokasi',
        'status'
    ];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }
}
