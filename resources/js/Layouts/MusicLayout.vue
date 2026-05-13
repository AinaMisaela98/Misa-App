<script setup>
import { Link, usePage } from '@inertiajs/vue3';

function goCours() {
    router.visit('/cours')
}

const user = usePage().props.auth.user;
</script>

<template>
    <div class="min-h-screen flex bg-gray-900 text-white">

        <!-- SIDEBAR -->
        <div class="w-64 bg-black p-5 flex flex-col justify-between">

            <div>
                <h1 class="text-2xl font-bold text-yellow-400 mb-8">
                    🎸 Misa Music
                </h1>

                <!-- MENU -->
                <nav class="space-y-4">
     
                    <Link href="/dashboard" class="block hover:text-yellow-400">
                        🏠 Dashboard
                    </Link>

                    <Link href="/cours" class="block hover:text-yellow-400">
                        🎵 Cours
                    </Link>
                    <Link href="/etudiants" class="block hover:text-yellow-400">
                        👤 Liste étudiants
                    </Link>

                    <Link href="#" class="block hover:text-yellow-400">
                        🎸 Guitare
                    </Link>

                    <Link href="#" class="block hover:text-yellow-400">
                        🎤 Production
                    </Link>
                    <Link href="/chat" class="text-white hover:text-yellow-400">
                        💬 Chat
                    </Link>

                    <!-- PROFIL -->
                    <Link href="/profile" class="block hover:text-yellow-400 mt-4">
                        👤 Profil
                    </Link>

                </nav>
            </div>

            <!-- PROFIL + LOGOUT -->
            <div class="border-t border-gray-700 pt-4">

                <p class="text-sm text-gray-400">Connecté :</p>
                <p class="font-bold">{{ user.name }}</p>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="mt-3 w-full bg-red-600 hover:bg-red-700 py-2 rounded"
                >
                    Déconnexion
                </Link>

            </div>

        </div>

        <!-- CONTENT -->
        <div class="flex-1 p-10">
            <slot />
        </div>

    </div>
</template>