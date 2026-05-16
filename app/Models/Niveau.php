<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $fillable = [
        'nom_niveau',
    ];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}
