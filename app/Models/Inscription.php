<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Serie;

class Inscription extends Model
{
    protected $fillable = [
        'etudiant_id',

        'date_inscription',
        'type',

        'numero_matricule',

        'nom',
        'prenoms',
        'genre',

        'date_naissance',
        'lieu_naissance',

        'signe_particulier',
        'allergique',
        'ecole_origine',

        'telephone',
        'contact',
        'adresse',
        'email',

        'code_postal',
        'ville',

        'taille',
        'poids',

        'photo',

        'pere_nom',
        'pere_telephone',
        'pere_profession',

        'mere_nom',
        'mere_telephone',
        'mere_profession',

        'tuteur_nom',
        'tuteur_telephone',
        'tuteur_lien',
        'tuteur_adresse',

        'niveau',
        'formation',

        'classe',
        'section',

        'classe_id',
        'serie_id',

        'annee_scolaire',

        'frais_inscription',
        'frais_formation',

        'mode_paiement',

        'activites',
        'observation',
    ];

    protected $casts = [
        'activites' => 'array',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }
}
