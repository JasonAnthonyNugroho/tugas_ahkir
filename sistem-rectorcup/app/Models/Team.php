<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'prodi'];

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_teams');
    }
}
