<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ClasseController extends Controller
{
    // =========================
    // LISTE DES CLASSES PAR ANNÉE ACTIVE
    // =========================
    public function index()
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $this->getAnneeValue($anneeActive);

        $classes = Classe::with(['niveau', 'series'])
            ->when($anneeScolaire && Schema::hasColumn('classes', 'annee_scolaire'), function ($query) use ($anneeScolaire) {
                $query->where('annee_scolaire', $anneeScolaire);
            })
            ->latest()
            ->get();

        return Inertia::render('Classes/Index', [
            'classes' => $classes,

            'niveaux' => Niveau::orderBy('nom_niveau')->get(),

            'anneeActive' => $anneeScolaire,
        ]);
    }

    // =========================
    // AJOUT CLASSE AVEC ANNÉE ACTIVE
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'nom_classe' => 'required|string|max:255',
        ]);

        $anneeActive = AnneeScolaire::where('active', 1)->first();

        $anneeScolaire = $this->getAnneeValue($anneeActive)
            ?? date('Y') . '-' . (date('Y') + 1);

        $payload = [
            'niveau_id' => $validated['niveau_id'],
            'nom_classe' => $validated['nom_classe'],
            'annee_scolaire' => $anneeScolaire,
        ];

        $columns = Schema::getColumnListing('classes');

        Classe::create(
            collect($payload)->only($columns)->toArray()
        );

        return redirect()->back()
            ->with('success', 'Classe ajoutée avec succès.');
    }

    // =========================
    // MODIFICATION CLASSE
    // =========================
    public function update(Request $request, Classe $class)
    {
        $validated = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'nom_classe' => 'required|string|max:255',
        ]);

        $payload = [
            'niveau_id' => $validated['niveau_id'],
            'nom_classe' => $validated['nom_classe'],
        ];

        $columns = Schema::getColumnListing('classes');

        $class->update(
            collect($payload)->only($columns)->toArray()
        );

        return redirect()->back()
            ->with('success', 'Classe modifiée avec succès.');
    }

    // =========================
    // SUPPRESSION CLASSE
    // =========================
    public function destroy(Classe $class)
    {
        $class->delete();

        return redirect()->back()
            ->with('success', 'Classe supprimée avec succès.');
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
