<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NiveauController extends Controller
{
    public function index()
    {
        return Inertia::render('Niveaux/Index', [
            'niveaux' => Niveau::with('classes')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_niveau' => 'required|string|max:255',
        ]);

        Niveau::create($validated);

        return redirect()->back()->with('success', 'Niveau ajouté avec succès.');
    }

    public function update(Request $request, Niveau $niveau)
    {
        $validated = $request->validate([
            'nom_niveau' => 'required|string|max:255',
        ]);

        $niveau->update($validated);

        return redirect()->back()->with('success', 'Niveau modifié avec succès.');
    }

    public function destroy(Niveau $niveau)
    {
        $niveau->delete();

        return redirect()->back()->with('success', 'Niveau supprimé avec succès.');
    }
}
