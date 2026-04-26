<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['nama_sport', 'icon'];

    public function pertandingans()
    {
        return $this->hasMany(Pertandingan::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }
}
