<?php

namespace App\Http\Controllers;

use App\Models\ParametreMatricule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParametreMatriculeController extends Controller
{
    // =========================
    // LISTE
    // =========================
    public function index()
    {
        return Inertia::render('Parametres/Matricule', [
            'matricules' => ParametreMatricule::orderBy('id', 'desc')->get(),
            'active' => ParametreMatricule::where('active', 1)->first(),
        ]);
    }

    // =========================
    // AJOUT
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:20',
        ]);

        ParametreMatricule::create([
            'prefix' => strtoupper($validated['prefix']),
            'active' => 0,
        ]);

        return redirect()
            ->route('parametres.matricule.index')
            ->with('success', 'Préfixe matricule ajouté.');
    }

    // =========================
    // ACTIVATION
    // =========================
    public function activate($id)
    {
        ParametreMatricule::query()->update([
            'active' => 0,
        ]);

        $matricule = ParametreMatricule::findOrFail($id);

        $matricule->update([
            'active' => 1,
        ]);

        return redirect()
            ->route('parametres.matricule.index')
            ->with('success', 'Préfixe activé avec succès.');
    }

    // =========================
    // SUPPRESSION
    // =========================
    public function destroy($id)
    {
        $matricule = ParametreMatricule::findOrFail($id);

        $matricule->delete();

        return redirect()
            ->route('parametres.matricule.index')
            ->with('success', 'Préfixe supprimé avec succès.');
    }
}
