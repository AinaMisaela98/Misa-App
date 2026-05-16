<?php

namespace App\Http\Controllers;

use App\Exports\EtudiantsExport;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\ParametreMatricule;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class EtudiantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $anneeFilter = $request->annee_scolaire;

        $anneeActive = AnneeScolaire::where('active', 1)->first();
        $anneeParDefaut = $this->getAnneeValue($anneeActive);

        $anneeSelectionnee = $anneeFilter ?: $anneeParDefaut;

        $etudiants = Etudiant::query()
            ->with(['classe.niveau', 'serie', 'inscription'])
            ->when($anneeSelectionnee, function ($query) use ($anneeSelectionnee) {
                $query->where('annee_scolaire', $anneeSelectionnee);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenoms', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%")
                        ->orWhere('contact', 'like', "%{$search}%")
                        ->orWhere('numero', 'like', "%{$search}%")
                        ->orWhere('classe', 'like', "%{$search}%")
                        ->orWhere('niveau', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%")
                        ->orWhere('annee_scolaire', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Etudiants/Index', [
            'etudiants' => $etudiants,
            'classes' => Classe::with(['niveau', 'series'])->orderBy('nom_classe')->get(),
            'series' => Serie::orderBy('nom_serie')->get(),
            'anneeScolaires' => AnneeScolaire::orderBy('id', 'desc')->get(),
            'anneeActive' => $anneeParDefaut,
            'filters' => [
                'search' => $search,
                'annee_scolaire' => $anneeSelectionnee,
            ],
        ]);
    }

    public function stats()
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();
        $annee = $this->getAnneeValue($anneeActive);

        $base = Etudiant::query()
            ->when($annee, function ($query) use ($annee) {
                $query->where('annee_scolaire', $annee);
            });

        return response()->json([
            'total' => (clone $base)->count(),
            'active' => Schema::hasColumn('etudiants', 'status')
                ? (clone $base)->where('status', 'active')->count()
                : 0,
            'inactive' => Schema::hasColumn('etudiants', 'status')
                ? (clone $base)->where('status', 'inactive')->count()
                : 0,
            'debutant' => (clone $base)->where('niveau', 'Débutant')->count(),
            'intermediaire' => (clone $base)->where('niveau', 'Intermédiaire')->count(),
            'avance' => (clone $base)->where('niveau', 'Avancé')->count(),
        ]);
    }

    public function byNiveau($niveau)
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();
        $annee = $this->getAnneeValue($anneeActive);

        $etudiants = Etudiant::with(['classe.niveau', 'serie', 'inscription'])
            ->where('niveau', $niveau)
            ->when($annee, function ($query) use ($annee) {
                $query->where('annee_scolaire', $annee);
            })
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Etudiants/ByNiveau', [
            'etudiants' => $etudiants,
            'niveau' => $niveau,
            'anneeActive' => $annee,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'sexe' => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'serie_id' => 'nullable|exists:series,id',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:255',
            'adresse' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $anneeActive = AnneeScolaire::where('active', 1)->first();

            $annee = $this->getAnneeValue($anneeActive)
                ?? date('Y') . '-' . (date('Y') + 1);

            $numero = $this->generateUniqueNumero();

            $classe = Classe::with('niveau')->find($validated['classe_id']);
            $serie = !empty($validated['serie_id']) ? Serie::find($validated['serie_id']) : null;

            $payload = [
                'numero' => $numero,
                'numero_matricule' => $numero,
                'nom' => $validated['nom'],
                'prenoms' => $validated['prenoms'],
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'] ?? null,
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'classe_id' => $validated['classe_id'],
                'serie_id' => $validated['serie_id'] ?? null,
                'classe' => $classe?->nom_classe,
                'niveau' => $classe?->niveau?->nom_niveau,
                'section' => $serie?->nom_serie,
                'email' => $validated['email'] ?? null,
                'telephone' => $validated['telephone'] ?? null,
                'contact' => $validated['telephone'] ?? null,
                'adresse' => $validated['adresse'],
                'annee_scolaire' => $annee,
                'site' => 'Strelitzia School',
                'status' => 'actif',
            ];

            $columns = Schema::getColumnListing('etudiants');

            Etudiant::create(
                collect($payload)->only($columns)->toArray()
            );
        });

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant ajouté avec succès.');
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'sexe' => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'serie_id' => 'nullable|exists:series,id',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:255',
            'adresse' => 'required|string|max:255',
            'photo' => 'nullable|image|max:4096',
        ]);

        $classe = Classe::with('niveau')->find($validated['classe_id']);
        $serie = !empty($validated['serie_id']) ? Serie::find($validated['serie_id']) : null;

        $photoPath = $etudiant->photo;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('etudiants', 'public');
        }

        $payload = [
            'nom' => $validated['nom'],
            'prenoms' => $validated['prenoms'],
            'sexe' => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'] ?? null,
            'lieu_naissance' => $validated['lieu_naissance'] ?? null,
            'classe_id' => $validated['classe_id'],
            'serie_id' => $validated['serie_id'] ?? null,
            'classe' => $classe?->nom_classe,
            'niveau' => $classe?->niveau?->nom_niveau,
            'section' => $serie?->nom_serie,
            'email' => $validated['email'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'contact' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'],
            'photo' => $photoPath,
        ];

        $columns = Schema::getColumnListing('etudiants');

        $etudiant->update(
            collect($payload)->only($columns)->toArray()
        );

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant modifié avec succès.');
    }

    public function updateParents(Request $request, Etudiant $etudiant)
    {
        $validated = $request->validate([
            'pere_nom' => 'nullable|string|max:255',
            'pere_telephone' => 'nullable|string|max:255',
            'pere_profession' => 'nullable|string|max:255',
            'mere_nom' => 'nullable|string|max:255',
            'mere_telephone' => 'nullable|string|max:255',
            'mere_profession' => 'nullable|string|max:255',
            'tuteur_nom' => 'nullable|string|max:255',
            'tuteur_telephone' => 'nullable|string|max:255',
            'tuteur_lien' => 'nullable|string|max:255',
            'tuteur_adresse' => 'nullable|string|max:255',
        ]);

        if ($etudiant->inscription) {
            $columns = Schema::getColumnListing('inscriptions');

            $etudiant->inscription->update(
                collect($validated)->only($columns)->toArray()
            );
        }

        return redirect()->route('etudiants.index')
            ->with('success', 'Informations parents modifiées.');
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant supprimé avec succès.');
    }

    public function exportByNiveau($niveau)
    {
        return Excel::download(
            new EtudiantsExport($niveau),
            "etudiants-$niveau.xlsx"
        );
    }

    private function getAnneeValue($anneeActive): ?string
    {
        if (!$anneeActive) {
            return null;
        }

        return $anneeActive->annee
            ?? $anneeActive->nom
            ?? $anneeActive->libelle
            ?? null;
    }

    private function generateUniqueNumero(): string
    {
        $matriculeActive = ParametreMatricule::where('active', 1)->first();

        $prefix = $matriculeActive?->prefix ?? 'ST';
        $prefix = strtoupper(trim($prefix));
        $prefix = preg_replace('/[^A-Z0-9]/', '', $prefix);

        if ($prefix === '') {
            $prefix = 'ST';
        }

        $last = Etudiant::where('numero', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if (
            $last &&
            !empty($last->numero) &&
            preg_match('/(\d+)$/', $last->numero, $matches)
        ) {
            $nextNumber = intval($matches[1]) + 1;
        }

        do {
            $numero = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Etudiant::where('numero', $numero)->exists());

        return $numero;
    }
}
