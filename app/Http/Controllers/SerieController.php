<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class SerieController extends Controller
{
    // =========================
    // LISTE DES SÉRIES PAR ANNÉE ACTIVE
    // =========================
    public function index()
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $this->getAnneeValue($anneeActive);

        $series = Serie::with(['classe.niveau'])
            ->when($anneeScolaire && Schema::hasColumn('series', 'annee_scolaire'), function ($query) use ($anneeScolaire) {
                $query->where('annee_scolaire', $anneeScolaire);
            })
            ->latest()
            ->get();

        $classes = Classe::with('niveau')
            ->when($anneeScolaire && Schema::hasColumn('classes', 'annee_scolaire'), function ($query) use ($anneeScolaire) {
                $query->where('annee_scolaire', $anneeScolaire);
            })
            ->orderBy('nom_classe')
            ->get();

        return Inertia::render('Series/Index', [
            'series' => $series,
            'classes' => $classes,
            'anneeActive' => $anneeScolaire,
        ]);
    }

    // =========================
    // AJOUT SÉRIE AVEC ANNÉE ACTIVE
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'nom_serie' => 'required|string|max:255',
        ]);

        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $this->getAnneeValue($anneeActive)
            ?? date('Y') . '-' . (date('Y') + 1);

        $payload = [
            'classe_id' => $validated['classe_id'],
            'nom_serie' => $validated['nom_serie'],
            'annee_scolaire' => $anneeScolaire,
        ];

        $columns = Schema::getColumnListing('series');

        Serie::create(
            collect($payload)->only($columns)->toArray()
        );

        return redirect()->back()
            ->with('success', 'Série ajoutée avec succès.');
    }

    // =========================
    // MODIFICATION SÉRIE
    // =========================
    public function update(Request $request, Serie $series)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'nom_serie' => 'required|string|max:255',
        ]);

        $payload = [
            'classe_id' => $validated['classe_id'],
            'nom_serie' => $validated['nom_serie'],
        ];

        $columns = Schema::getColumnListing('series');

        $series->update(
            collect($payload)->only($columns)->toArray()
        );

        return redirect()->back()
            ->with('success', 'Série modifiée avec succès.');
    }

    // =========================
    // SUPPRESSION SÉRIE
    // =========================
    public function destroy(Serie $series)
    {
        $series->delete();

        return redirect()->back()
            ->with('success', 'Série supprimée avec succès.');
    }

    // =========================
    // RÉCUPÉRATION ANNÉE ACTIVE
    // =========================
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
}
