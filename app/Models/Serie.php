<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = [
        'classe_id',
        'nom_serie',
        'annee_scolaire',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function etudiants()
    {
        return $this->hasMany(Etudiant::class);
    }
}
