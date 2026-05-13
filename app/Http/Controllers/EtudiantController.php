<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\EtudiantsExport;
use Maatwebsite\Excel\Facades\Excel;



class EtudiantController extends Controller
  // 📋 CONTROL
{
    public function stats()

    {
    return response()->json([
        'total' => Etudiant::count(),
        'active' => Etudiant::where('status', 'active')->count(),
        'inactive' => Etudiant::where('status', 'inactive')->count(),
    ]);


    return response()->json([

        'total' => \App\Models\Etudiant::count(),

        'debutant' => \App\Models\Etudiant::where('niveau', 'Débutant')->count(),

        'intermediaire' => \App\Models\Etudiant::where('niveau', 'Intermédiaire')->count(),

        'avance' => \App\Models\Etudiant::where('niveau', 'Avancé')->count(),

    ]);
}

    // 📋 LISTE + SEARCH
    public function index(Request $request)
    {
        $search = $request->search;

        $etudiants = Etudiant::query()
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return Inertia::render('Etudiants/Index', [
            'etudiants' => $etudiants,
            'filters' => [
                'search' => $search
            ]
        ]);
    }
  
 // ➕ BY NIVEAU
    public function byNiveau($niveau)
{
    $etudiants = Etudiant::where('niveau', $niveau)->latest()->get();

    return Inertia::render('Etudiants/ByNiveau', [
        'etudiants' => $etudiants,
        'niveau' => $niveau
    ]);
}
    // ➕ CREATE
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'required|string|max:255',
            'niveau' => 'required|string|max:50',
        ]);
            // 🔥 Maka numéro very
    $usedNumbers = Etudiant::orderBy('numero')->pluck('numero')->toArray();

    $numero = 1;

    while (in_array($numero, $usedNumbers)) {
        $numero++;
    }


        Etudiant::create([
            'numero' => $numero,
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse, 
            'niveau' => $request->niveau,
        ]);

        return redirect()->back();
    }

    // EXPORTATION EXCEL
public function exportByNiveau($niveau)
{
    return Excel::download(
        new EtudiantsExport($niveau),
        "etudiants-$niveau.xlsx"
    );
}
    // ✏️ UPDATE
    public function update(Request $request, Etudiant $etudiant)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'required|string|max:255',
            'niveau' => 'required|string|max:50',
        ]);

        $etudiant->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'niveau' => $request->niveau,
        ]);

        return redirect()->back();
    }

    // 🗑️ DELETE
    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();

        return redirect()->back();
    }
}