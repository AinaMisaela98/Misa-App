<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\ParametreMatricule;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class InscriptionController extends Controller
{
    private string $sessionKey = 'inscription_data';

    public function create()
    {
        return Inertia::render('Inscriptions/Create', [
            'data' => session($this->sessionKey, []),
            'matricules' => ParametreMatricule::orderBy('id', 'desc')->get(),
            'matriculeActive' => ParametreMatricule::where('active', 1)->first(),
            'anneeActive' => AnneeScolaire::where('active', 1)->first(),
        ]);
    }

    public function storeEtudiant(Request $request)
    {
        $validated = $request->validate([
            'date_inscription' => 'nullable|date',

            'matricule_id' => 'required|exists:parametre_matricules,id',
            'numero_matricule' => 'required|string|max:255',

            'nom' => 'required|string|max:255',
            'prenoms' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'signe_particulier' => 'nullable|string|max:255',
            'allergique' => 'nullable|string|max:255',
            'ecole_origine' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'adresse' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'code_postal' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'taille' => 'nullable|string|max:255',
            'poids' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:4096',
        ], [
            'matricule_id.required' => 'Veuillez choisir un modèle de matricule.',
            'numero_matricule.required' => 'Veuillez saisir le numéro matricule.',
        ]);

        $matricule = ParametreMatricule::find($validated['matricule_id']);
        $prefix = strtoupper(trim($matricule?->prefix ?? ''));

        $numeroManuel = strtoupper(trim($validated['numero_matricule']));
        $validated['numero_matricule'] = $numeroManuel;

        if ($prefix !== '' && !str_starts_with($numeroManuel, $prefix)) {
            return back()->withErrors([
                'numero_matricule' => "Le matricule doit commencer par le modèle choisi : {$prefix}",
            ])->withInput();
        }

        $exists = Etudiant::where('numero', $numeroManuel)->exists()
            || Inscription::where('numero_matricule', $numeroManuel)->exists();

        if ($exists) {
            return back()->withErrors([
                'numero_matricule' => 'Ce numéro matricule existe déjà.',
            ])->withInput();
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('inscriptions', 'public');
        } elseif (session($this->sessionKey . '.photo')) {
            $validated['photo'] = session($this->sessionKey . '.photo');
        }

        if (empty($validated['date_inscription'])) {
            $validated['date_inscription'] = now()->toDateString();
        }

        session([
            $this->sessionKey => array_merge(session($this->sessionKey, []), $validated),
        ]);

        return redirect()->route('inscriptions.parents');
    }

    public function parents()
    {
        return Inertia::render('Inscriptions/Parents', [
            'data' => session($this->sessionKey, []),
        ]);
    }

    public function storeParents(Request $request)
    {
        $validated = $request->validate([
            'pere_nom' => 'nullable|string|max:255',
            'pere_telephone' => 'nullable|string|max:255',
            'pere_profession' => 'nullable|string|max:255',
            'mere_nom' => 'nullable|string|max:255',
            'mere_telephone' => 'nullable|string|max:255',
            'mere_profession' => 'nullable|string|max:255',
        ]);

        session([
            $this->sessionKey => array_merge(session($this->sessionKey, []), $validated),
        ]);

        return redirect()->route('inscriptions.tuteurs');
    }

    public function tuteurs()
    {
        return Inertia::render('Inscriptions/Tuteurs', [
            'data' => session($this->sessionKey, []),
        ]);
    }

    public function storeTuteurs(Request $request)
    {
        $validated = $request->validate([
            'tuteur_nom' => 'nullable|string|max:255',
            'tuteur_telephone' => 'nullable|string|max:255',
            'tuteur_lien' => 'nullable|string|max:255',
            'tuteur_adresse' => 'nullable|string|max:255',
        ]);

        session([
            $this->sessionKey => array_merge(session($this->sessionKey, []), $validated),
        ]);

        return redirect()->route('inscriptions.niveau');
    }

    public function niveau()
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $anneeActive?->annee
            ?? $anneeActive?->nom
            ?? null;

        return Inertia::render('Inscriptions/Niveau', [
            'data' => session($this->sessionKey, []),

            'classes' => Classe::with(['series' => function ($query) use ($anneeScolaire) {
                if ($anneeScolaire) {
                    $query->where('annee_scolaire', $anneeScolaire);
                }
            }, 'niveau'])
                ->when($anneeScolaire, function ($query) use ($anneeScolaire) {
                    $query->where('annee_scolaire', $anneeScolaire);
                })
                ->orderBy('nom_classe')
                ->get(),

            'series' => Serie::when($anneeScolaire, function ($query) use ($anneeScolaire) {
                $query->where('annee_scolaire', $anneeScolaire);
            })
                ->orderBy('nom_serie')
                ->get(),

            'anneeActive' => $anneeScolaire,
        ]);
    }

    public function storeNiveau(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'nullable',
            'serie_id' => 'nullable',
            'formation' => 'nullable|string|max:255',
            'frais_inscription' => 'nullable|numeric',
            'frais_formation' => 'nullable|numeric',
            'mode_paiement' => 'nullable|string|max:255',
        ]);

        $classe = !empty($validated['classe_id'])
            ? Classe::with('niveau')->find($validated['classe_id'])
            : null;

        $serie = !empty($validated['serie_id'])
            ? Serie::find($validated['serie_id'])
            : null;

        $validated['classe'] = $classe?->nom_classe ?? $classe?->nom ?? null;
        $validated['niveau'] = $classe?->niveau?->nom_niveau ?? $classe?->niveau?->nom ?? null;
        $validated['serie'] = $serie?->nom_serie ?? $serie?->nom ?? null;
        $validated['section'] = $validated['serie'];

        session([
            $this->sessionKey => array_merge(session($this->sessionKey, []), $validated),
        ]);

        return redirect()->route('inscriptions.activites');
    }

    public function activites()
    {
        return Inertia::render('Inscriptions/Activites', [
            'data' => session($this->sessionKey, []),
        ]);
    }

    public function storeActivites(Request $request)
    {
        $validated = $request->validate([
            'activites' => 'nullable|array',
            'observation' => 'nullable|string',
        ]);

        session([
            $this->sessionKey => array_merge(session($this->sessionKey, []), $validated),
        ]);

        return redirect()->route('inscriptions.validation');
    }

    public function validation()
    {
        return Inertia::render('Inscriptions/Validation', [
            'data' => session($this->sessionKey, []),
        ]);
    }

    public function finaliser()
    {
        $data = session($this->sessionKey, []);

        if (empty($data['nom']) || empty($data['adresse'])) {
            return redirect()->route('inscriptions.create')
                ->with('error', 'Information étudiant incomplète.');
        }

        if (empty($data['numero_matricule'])) {
            return redirect()->route('inscriptions.create')
                ->with('error', 'Numéro matricule obligatoire.');
        }

        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $anneeActive?->annee
            ?? $anneeActive?->nom
            ?? '2025-2026';

        $numero = strtoupper(trim($data['numero_matricule']));

        $exists = Etudiant::where('numero', $numero)->exists()
            || Inscription::where('numero_matricule', $numero)->exists();

        if ($exists) {
            return redirect()->route('inscriptions.create')
                ->with('error', 'Ce numéro matricule existe déjà.');
        }

        DB::transaction(function () use ($data, $anneeScolaire, $numero) {
            $etudiantPayload = [
                'numero' => $numero,
                'nom' => $data['nom'] ?? null,
                'prenoms' => $data['prenoms'] ?? null,
                'sexe' => $data['genre'] ?? null,
                'date_naissance' => $data['date_naissance'] ?? null,
                'lieu_naissance' => $data['lieu_naissance'] ?? null,
                'contact' => $data['telephone'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'email' => $data['email'] ?? null,
                'photo' => $data['photo'] ?? null,
                'classe_id' => $data['classe_id'] ?? null,
                'serie_id' => $data['serie_id'] ?? null,
                'classe' => $data['classe'] ?? null,
                'niveau' => $data['niveau'] ?? null,
                'section' => $data['serie'] ?? $data['section'] ?? null,
                'annee_scolaire' => $anneeScolaire,
                'site' => 'Strelitzia School',
            ];

            $etudiantColumns = Schema::getColumnListing('etudiants');

            $etudiant = Etudiant::create(
                collect($etudiantPayload)->only($etudiantColumns)->toArray()
            );

            $inscriptionPayload = array_merge($data, [
                'numero_matricule' => $numero,
                'etudiant_id' => $etudiant->id,
                'date_inscription' => $data['date_inscription'] ?? now()->toDateString(),
                'annee_scolaire' => $anneeScolaire,
                'classe' => $data['classe'] ?? null,
                'niveau' => $data['niveau'] ?? null,
                'section' => $data['serie'] ?? $data['section'] ?? null,
            ]);

            $inscriptionColumns = Schema::getColumnListing('inscriptions');

            Inscription::create(
                collect($inscriptionPayload)->only($inscriptionColumns)->toArray()
            );
        });

        session()->forget($this->sessionKey);

        return redirect()->route('etudiants.index')
            ->with('success', 'Inscription validée. Étudiant ajouté dans la liste.');
    }

    public function reset()
    {
        session()->forget($this->sessionKey);

        return redirect()->route('inscriptions.create');
    }
}
