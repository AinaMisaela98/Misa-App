<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([

            'classe_id' => 'required',
            'nom' => 'required'

        ]);

        Programme::create([

            'classe_id' => $request->classe_id,
            'nom' => $request->nom

        ]);

        return redirect()->back();
    }
}