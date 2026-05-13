<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeAdminUserSystem extends Command
{
    protected $signature = 'make:admin-system';
    protected $description = 'Generate Admin User Management System';

    public function handle()
    {
        $this->info("🚀 Creating Admin System...");

        // =========================
        // 1. Middleware
        // =========================
        File::ensureDirectoryExists(app_path('Http/Middleware'));

        File::put(app_path('Http/Middleware/IsAdmin.php'), <<<PHP
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request \$request, Closure \$next)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé');
        }

        return \$next(\$request);
    }
}
PHP);

        $this->info("✅ Middleware created");

        // =========================
        // 2. Controller
        // =========================
        File::ensureDirectoryExists(app_path('Http/Controllers/Admin'));

        File::put(app_path('Http/Controllers/Admin/UserAdminController.php'), <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index()
    {
        return inertia('Admin/Users', [
            'users' => User::latest()->get()
        ]);
    }

    public function update(Request \$request, \$id)
    {
        \$user = User::findOrFail(\$id);

        \$user->update([
            'name' => \$request->name,
            'email' => \$request->email,
            'role' => \$request->role,
        ]);

        return back();
    }

    public function destroy(\$id)
    {
        User::findOrFail(\$id)->delete();
        return back();
    }
}
PHP);

        $this->info("✅ Controller created");

        // =========================
        // 3. Vue Page
        // =========================
        File::ensureDirectoryExists(resource_path('js/Pages/Admin'));

        File::put(resource_path('js/Pages/Admin/Users.vue'), <<<VUE
<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
    users: Array
})

const deleteUser = (id) => {
    if (confirm('Delete this user?')) {
        router.delete('/admin/users/' + id)
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-950 text-white p-6">

        <h1 class="text-3xl font-bold mb-6">👑 Admin Users</h1>

        <table class="w-full border border-gray-700">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-2">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="user in users" :key="user.id" class="border-t border-gray-700">
                    <td class="p-2">{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.role }}</td>
                    <td class="flex gap-2 p-2">
                        <button class="bg-blue-500 px-2 py-1 rounded">Edit</button>
                        <button @click="deleteUser(user.id)" class="bg-red-500 px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</template>
VUE);

        $this->info("✅ Vue page created");

        $this->warn("👉 Next steps:");
        $this->line("1. Add role column to users table");
        $this->line("2. Register middleware in Kernel.php");
        $this->line("3. Add routes admin/users");
        $this->line("4. php artisan migrate");

        $this->info("🎉 ADMIN SYSTEM GENERATED SUCCESSFULLY!");
    }
}