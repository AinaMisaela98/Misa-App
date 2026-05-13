<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <!-- SIDEBAR -->
        <div class="fixed left-0 top-0 h-full w-72 bg-gradient-to-b from-purple-950 to-black p-6 shadow-2xl">
            <h1 class="mb-10 text-3xl font-black text-pink-400">
                👑 ADMIN PANEL
            </h1>

            <div class="space-y-3">
                <Link
                    href="/admin"
                    class="block rounded-2xl bg-white/10 px-5 py-4 font-bold hover:bg-pink-500"
                >
                    📊 Dashboard
                </Link>

                <Link
                    href="/admin/users"
                    class="block rounded-2xl bg-white/10 px-5 py-4 font-bold hover:bg-pink-500"
                >
                    👥 Utilisateurs
                </Link>

                <Link
                    href="/etudiants"
                    class="block rounded-2xl bg-white/10 px-5 py-4 font-bold hover:bg-pink-500"
                >
                    🎓 Étudiants
                </Link>

                <Link
                    href="/chat"
                    class="block rounded-2xl bg-white/10 px-5 py-4 font-bold hover:bg-pink-500"
                >
                    💬 Chat
                </Link>

                <Link
                    href="/profile"
                    class="block rounded-2xl bg-white/10 px-5 py-4 font-bold hover:bg-pink-500"
                >
                    ⚙️ Profil
                </Link>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="block w-full rounded-2xl bg-red-600 px-5 py-4 font-bold hover:bg-red-500"
                >
                    🚪 Logout
                </Link>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="ml-72 p-10">
            <slot />
        </div>
    </div>
</template>
