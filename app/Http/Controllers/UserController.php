<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function admin()
    {
        return Inertia::render('Admin/Index', [
            'users' => User::latest()->get(),
        ]);
    }

    public function index()
    {
        return Inertia::render('Admin/Index', [
            'users' => User::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Edit', [
            'user' => $user,
        ]);
    }

    /*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/

public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8',
        'role' => 'required|in:admin,user',
    ]);

    // Raha misy password vaovao
    if (!empty($validated['password'])) {

        $validated['password'] = Hash::make($validated['password']);

    } else {

        // Raha vide dia tsy ovaina ilay password
        unset($validated['password']);
    }

    // Update database
    $user->update($validated);

    // Retour admin page
    return redirect()
        ->route('admin.index')
        ->with('success', 'Utilisateur modifié avec succès.');
}

/*
|--------------------------------------------------------------------------
| DELETE USER
|--------------------------------------------------------------------------
*/

public function destroy(User $user)
{
    // Tsy afaka mamafa ny compte-ny manokana
    if (auth()->id() === $user->id) {

        return back()->withErrors([
            'delete' => 'Vous ne pouvez pas supprimer votre propre compte.',
        ]);
    }

    // Suppression
    $user->delete();

    // Redirect
    return redirect()
        ->route('admin.index')
        ->with('success', 'Utilisateur supprimé avec succès.');
  }
}
