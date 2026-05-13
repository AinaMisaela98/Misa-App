<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD + ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/admin', [UserController::class, 'admin'])
        ->name('admin.index');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('admin.users.store');

    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');

    Route::get('/force-logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('force.logout');
});

/*
|--------------------------------------------------------------------------
| CHAT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

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
});

/*
|--------------------------------------------------------------------------
| ETUDIANTS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/etudiants', [EtudiantController::class, 'index'])
        ->name('etudiants');

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
});

/*
|--------------------------------------------------------------------------
| COURS
|--------------------------------------------------------------------------
*/

Route::get('/cours', function () {
    return Inertia::render('Cours');
})->middleware(['auth'])->name('cours');


/*
|--------------------------------------------------------------------------
| photo de profil
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

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
            'email' => 'required|email|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile', 'public');
            $user->photo = $path;
            $user->save();
        }

        return back();
    })->name('profile.custom.update');

    Route::post('/profile/password', function (Request $request) {

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        $user->password = Hash::make($request->password);
        $user->save();

        return back();
    })->name('profile.password.update');
});

require __DIR__.'/auth.php';
