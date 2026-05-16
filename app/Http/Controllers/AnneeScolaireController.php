<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        return Inertia::render('Parametres/AnneeScolaire', [
            'annees' => AnneeScolaire::orderBy('id', 'desc')->get(),
            'active' => AnneeScolaire::where('active', 1)->first(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'annee' => 'required|string|max:255',
        ]);

        AnneeScolaire::create([
            'annee' => $request->annee,
            'active' => 0,
        ]);

        return redirect()
            ->route('parametres.annee.index')
            ->with('success', 'Année scolaire ajoutée avec succès.');
    }

    public function activate($id)
    {
        AnneeScolaire::query()->update([
            'active' => 0,
        ]);

        $annee = AnneeScolaire::findOrFail($id);

        $annee->update([
            'active' => 1,
        ]);

        return redirect()
            ->route('parametres.annee.index')
            ->with('success', 'Année scolaire activée avec succès.');
    }

    public function destroy($id)
    {
        $annee = AnneeScolaire::findOrFail($id);
        $annee->delete();

        return redirect()
            ->route('parametres.annee.index')
            ->with('success', 'Année scolaire supprimée avec succès.');
    }
}
