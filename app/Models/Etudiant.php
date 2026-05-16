<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Classe;
use App\Models\Serie;
use App\Models\Inscription;

class Etudiant extends Model
{
    protected $fillable = [
        'numero',
        'nom',
        'prenoms',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'contact',
        'telephone',
        'adresse',
        'email',
        'photo',

        'classe_id',
        'serie_id',
        'classe',
        'niveau',
        'section',

        'annee_scolaire',
        'site',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }

    public function inscription()
    {
        return $this->hasOne(Inscription::class, 'etudiant_id');
    }
}
