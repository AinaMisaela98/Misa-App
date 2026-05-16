<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Serie;
use App\Models\Inscription;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ReinscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Reinscriptions/Index', [
            'etudiants' => Etudiant::orderBy('nom')->get(),

            'classes' => Classe::with(['series', 'niveau'])->get(),

            'anneeScolaires' => AnneeScolaire::orderByDesc('id')->get(),

            'reinscriptions' => Inscription::with(['etudiant', 'classe', 'serie'])
                ->where('type', 'reinscription')
                ->latest()
                ->get(),
        ]);
    }

    public function viderHistorique()
    {
        Inscription::where('type', 'reinscription')->delete();

        return back()->with('success', 'Historique supprimé définitivement.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'classe_id' => 'required|exists:classes,id',
            'serie_id' => 'nullable|exists:series,id',
            'annee_scolaire' => 'required|string',
        ]);

        $ancienEtudiant = Etudiant::with('inscription')->findOrFail($request->etudiant_id);

        $exists = Etudiant::where(function ($query) use ($ancienEtudiant) {
                $query->where('numero', $ancienEtudiant->numero)
                    ->orWhere(function ($q) use ($ancienEtudiant) {
                        $q->where('nom', $ancienEtudiant->nom)
                            ->where('prenoms', $ancienEtudiant->prenoms)
                            ->where('date_naissance', $ancienEtudiant->date_naissance);
                    });
            })
            ->where('annee_scolaire', $request->annee_scolaire)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'etudiant_id' => '❌ Déjà inscrit pour cette année scolaire.',
            ]);
        }

        return DB::transaction(function () use ($request, $ancienEtudiant) {
            $ancienneInscription = $ancienEtudiant->inscription;

            $classe = Classe::with('niveau')->findOrFail($request->classe_id);
            $serie = $request->serie_id ? Serie::find($request->serie_id) : null;

            $nouvelEtudiant = Etudiant::create([
                'numero' => $ancienEtudiant->numero,

                'nom' => $ancienEtudiant->nom,
                'prenoms' => $ancienEtudiant->prenoms,
                'sexe' => $ancienEtudiant->sexe,

                'date_naissance' => $ancienEtudiant->date_naissance,
                'lieu_naissance' => $ancienEtudiant->lieu_naissance,

                'contact' => $ancienEtudiant->contact,
                'telephone' => $ancienEtudiant->telephone,
                'adresse' => $ancienEtudiant->adresse,
                'email' => $ancienEtudiant->email,
                'photo' => $ancienEtudiant->photo,
                'site' => $ancienEtudiant->site,

                'classe_id' => $request->classe_id,
                'serie_id' => $request->serie_id,
                'annee_scolaire' => $request->annee_scolaire,

                'classe' => $classe->nom_classe,
                'niveau' => $classe->niveau->nom_niveau ?? null,
                'section' => $serie->nom_serie ?? null,
            ]);

            Inscription::create([
                'etudiant_id' => $nouvelEtudiant->id,

                'nom' => $nouvelEtudiant->nom,
                'prenoms' => $nouvelEtudiant->prenoms,
                'genre' => $nouvelEtudiant->sexe,

                'date_naissance' => $nouvelEtudiant->date_naissance,
                'lieu_naissance' => $nouvelEtudiant->lieu_naissance,
                'telephone' => $nouvelEtudiant->telephone,
                'adresse' => $nouvelEtudiant->adresse,
                'email' => $nouvelEtudiant->email,

                'classe_id' => $request->classe_id,
                'serie_id' => $request->serie_id,
                'annee_scolaire' => $request->annee_scolaire,

                'classe' => $classe->nom_classe,
                'niveau' => $classe->niveau->nom_niveau ?? null,
                'section' => $serie->nom_serie ?? null,

                'pere_nom' => $ancienneInscription->pere_nom ?? null,
                'pere_telephone' => $ancienneInscription->pere_telephone ?? null,
                'pere_profession' => $ancienneInscription->pere_profession ?? null,

                'mere_nom' => $ancienneInscription->mere_nom ?? null,
                'mere_telephone' => $ancienneInscription->mere_telephone ?? null,
                'mere_profession' => $ancienneInscription->mere_profession ?? null,

                'tuteur_nom' => $ancienneInscription->tuteur_nom ?? null,
                'tuteur_telephone' => $ancienneInscription->tuteur_telephone ?? null,
                'tuteur_lien' => $ancienneInscription->tuteur_lien ?? null,
                'tuteur_adresse' => $ancienneInscription->tuteur_adresse ?? null,

                'date_inscription' => now()->format('Y-m-d'),
                'type' => 'reinscription',
            ]);

            return redirect('/reinscriptions')
                ->with('success', 'Réinscription effectuée avec succès.');
        });
    }
}
