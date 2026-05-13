<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserAdminController extends Controller
{
    public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return back()->with('success', 'User deleted');
}
    public function index(Request $request)
    {


        $search = $request->search;

        // 🔥 QUERY USERS SAFE
        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(6)
            ->withQueryString(); // 🔥 important pagination fix

        return Inertia::render('Admin/UsersAdmin', [
            'users' => $users,

            // 🔥 filters safe
            'filters' => [
                'search' => $search ?? ''
            ],

            // 🔥 stats dashboard
            'stats' => [
                'total_users' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'normal_users' => User::where('role', 'user')->count(),
            ]

        ]);
    }
}
