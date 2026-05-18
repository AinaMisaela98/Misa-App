<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\NiveauController;

use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ReinscriptionController;
use App\Http\Controllers\ParametreMatriculeController;
use App\Http\Controllers\AnneeScolaireController;
/*
|--------------------------------------------------------------------------
| PARAMÈTRES MATRICULE
|--------------------------------------------------------------------------
*/

Route::get('/parametres/matricule', [ParametreMatriculeController::class, 'index'])
    ->name('parametres.matricule.index');

Route::post('/parametres/matricule', [ParametreMatriculeController::class, 'store'])
    ->name('parametres.matricule.store');

Route::post('/parametres/matricule/{id}/activate', [ParametreMatriculeController::class, 'activate'])
    ->name('parametres.matricule.activate');

Route::delete('/parametres/matricule/{id}', [ParametreMatriculeController::class, 'destroy'])
    ->name('parametres.matricule.destroy');


/*
|--------------------------------------------------------------------------
| PARAMÈTRES ANNÉE SCOLAIRE
|--------------------------------------------------------------------------
*/

Route::get('/parametres/annee', [AnneeScolaireController::class, 'index'])
    ->name('parametres.annee.index');

Route::post('/parametres/annee', [AnneeScolaireController::class, 'store'])
    ->name('parametres.annee.store');

Route::post('/parametres/annee/{id}/activate', [AnneeScolaireController::class, 'activate'])
    ->name('parametres.annee.activate');

Route::delete('/parametres/annee/{id}', [AnneeScolaireController::class, 'destroy'])
    ->name('parametres.annee.destroy');


/*
|--------------------------------------------------------------------------
| REDIRECTION ANCIENNES ROUTES
|--------------------------------------------------------------------------
*/

Route::redirect('/matricule', '/parametres/matricule');

Route::redirect('/annee-scolaire', '/parametres/annee');



// routes/web.php
Route::post('/etudiants/{etudiant}/parents', [EtudiantController::class, 'updateParents'])
    ->name('etudiants.parents.update');


    Route::delete('/reinscriptions/historique/delete', [ReinscriptionController::class, 'viderHistorique'])
    ->name('reinscriptions.historique.delete');
/*
|--------------------------------------------------------------------------
| inscription
|--------------------------------------------------------------------------
*/


Route::prefix('inscriptions')->name('inscriptions.')->group(function () {
    Route::get('/', [InscriptionController::class, 'create'])->name('create');
    Route::post('/etudiant', [InscriptionController::class, 'storeEtudiant'])->name('etudiant.store');

    Route::get('/parents', [InscriptionController::class, 'parents'])->name('parents');
    Route::post('/parents', [InscriptionController::class, 'storeParents'])->name('parents.store');

    Route::get('/tuteurs', [InscriptionController::class, 'tuteurs'])->name('tuteurs');
    Route::post('/tuteurs', [InscriptionController::class, 'storeTuteurs'])->name('tuteurs.store');

    Route::get('/niveau', [InscriptionController::class, 'niveau'])->name('niveau');
    Route::post('/niveau', [InscriptionController::class, 'storeNiveau'])->name('niveau.store');

    Route::get('/activites', [InscriptionController::class, 'activites'])->name('activites');
    Route::post('/activites', [InscriptionController::class, 'storeActivites'])->name('activites.store');

    Route::get('/validation', [InscriptionController::class, 'validation'])->name('validation');
    Route::post('/finaliser', [InscriptionController::class, 'finaliser'])->name('finaliser');

    Route::get('/reset', [InscriptionController::class, 'reset'])->name('reset');
});

/*
|--------------------------------------------------------------------------
| REINSCRIPTION
|--------------------------------------------------------------------------
*/
Route::get('/reinscriptions', [ReinscriptionController::class, 'index'])->name('reinscriptions.index');
Route::post('/reinscriptions', [ReinscriptionController::class, 'store'])->name('reinscriptions.store');

/*
|--------------------------------------------------------------------------
| NIVEAUX
|--------------------------------------------------------------------------
*/

Route::get('/niveaux', [NiveauController::class, 'index'])
    ->name('niveaux.index');

Route::post('/niveaux', [NiveauController::class, 'store'])
    ->name('niveaux.store');

Route::put('/niveaux/{niveau}', [NiveauController::class, 'update'])
    ->name('niveaux.update');

Route::delete('/niveaux/{niveau}', [NiveauController::class, 'destroy'])
    ->name('niveaux.destroy');


/*
|--------------------------------------------------------------------------
| CLASSES
|--------------------------------------------------------------------------
*/

Route::get('/classes', [ClasseController::class, 'index'])
    ->name('classes.index');

Route::post('/classes', [ClasseController::class, 'store'])
    ->name('classes.store');

Route::put('/classes/{class}', [ClasseController::class, 'update'])
    ->name('classes.update');

Route::delete('/classes/{class}', [ClasseController::class, 'destroy'])
    ->name('classes.destroy');


/*
|--------------------------------------------------------------------------
| SERIES
|--------------------------------------------------------------------------
*/

Route::get('/series', [SerieController::class, 'index'])
    ->name('series.index');

Route::post('/series', [SerieController::class, 'store'])
    ->name('series.store');

Route::put('/series/{series}', [SerieController::class, 'update'])
    ->name('series.update');

Route::delete('/series/{series}', [SerieController::class, 'destroy'])
    ->name('series.destroy');


/*
|--------------------------------------------------------------------------
| CONTROLE CLASSE ET SERIE
|--------------------------------------------------------------------------
*/
Route::resource('niveaux', NiveauController::class);
Route::resource('classes', ClasseController::class);
Route::resource('series', SerieController::class);

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN USERS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin', [UserController::class, 'admin'])
        ->name('admin.index');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('admin.users.store');

    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');

    /*
    |--------------------------------------------------------------------------
    | ETUDIANTS
    |--------------------------------------------------------------------------
    */

    Route::get('/etudiants', [EtudiantController::class, 'index'])
        ->name('etudiants.index');

    Route::post('/etudiants', [EtudiantController::class, 'store'])
        ->name('etudiants.store');

    Route::put('/etudiants/{etudiant}', [EtudiantController::class, 'update'])
        ->name('etudiants.update');

    Route::delete('/etudiants/{etudiant}', [EtudiantController::class, 'destroy'])
        ->name('etudiants.destroy');

    Route::get('/etudiants/export/{niveau}', [EtudiantController::class, 'exportByNiveau'])
        ->name('etudiants.export');

    Route::get('/etudiants/niveau/{niveau}', [EtudiantController::class, 'byNiveau'])
        ->name('etudiants.niveau');

    Route::get('/stats-etudiants', [EtudiantController::class, 'stats'])
        ->name('etudiants.stats');

    /*
    |--------------------------------------------------------------------------
    | CHAT
    |--------------------------------------------------------------------------
    */

    Route::get('/chat', function () {
        return Inertia::render('Chat');
    })->name('chat');

    Route::get('/chat/users', [ChatController::class, 'users']);
    Route::get('/chat/messages/{id}', [ChatController::class, 'messages']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::post('/chat/send-many', [ChatController::class, 'sendMany']);

    Route::put('/chat/message/{id}', [ChatController::class, 'updateMessage']);
    Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage']);
    Route::delete('/chat/conversation/{id}', [ChatController::class, 'deleteConversation']);

    /*
    |--------------------------------------------------------------------------
    | COURS
    |--------------------------------------------------------------------------
    */

    Route::get('/cours', function () {
        return Inertia::render('Cours');
    })->name('cours');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('/profile/update', function (Request $request) {

        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile', 'public');
            $user->photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profil modifié avec succès.');
    })->name('profile.custom.update');

    Route::post('/profile/password', function (Request $request) {

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Mot de passe modifié avec succès.');
    })->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | FORCE LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/force-logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('force.logout');
});

require __DIR__.'/auth.php';
