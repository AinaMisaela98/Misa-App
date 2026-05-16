<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $fillable = [
        'niveau_id',
        'nom_classe',
        'annee_scolaire',
    ];
    public function niveau()
   {
    return $this->belongsTo(Niveau::class);
   }

    public function series()
    {
        return $this->hasMany(Serie::class);
    }

    public function etudiants()
    {
        return $this->hasMany(Etudiant::class);
    }
}
