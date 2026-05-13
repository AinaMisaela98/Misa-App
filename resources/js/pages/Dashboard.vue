<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import {
    LayoutDashboard,
    GraduationCap,
    BookOpen,
    MessageCircle,
    Users,
    Settings,
    LogOut,
    Bell,
    Search,
    Music,
    TrendingUp,
    CalendarDays,
    Sparkles,
} from 'lucide-vue-next'

defineOptions({
    layout: AuthenticatedLayout,
})

const page = usePage()

const user = computed(() => {
    return page.props.auth?.user ?? {
        name: 'Utilisateur',
        email: '',
        role: 'Utilisateur',
        photo: null,
    }
})

const userInitial = computed(() => {
    return user.value?.name
        ? user.value.name.charAt(0).toUpperCase()
        : 'U'
})

const photoUrl = computed(() => {
    if (!user.value?.photo) return null

    return `/storage/${user.value.photo}`
})
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white flex overflow-hidden">
        <aside class="w-[300px] hidden lg:flex flex-col justify-between border-r border-white/10 bg-slate-950/95 backdrop-blur-xl">
            <div>
                <div class="h-24 px-7 flex items-center gap-4 border-b border-white/10">
                    <div class="w-14 h-14 rounded-3xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-xl shadow-blue-500/30">
                        <Music class="w-8 h-8" />
                    </div>

                    <div>
                        <h1 class="text-2xl font-black tracking-tight">
                            Aina Misaela Music
                        </h1>
                        <p class="text-sm text-slate-400">
                            Gestion scolaire pro
                        </p>
                    </div>
                </div>

                <nav class="mt-8 px-5 space-y-2">
                    <Link href="/dashboard" class="flex items-center gap-4 px-5 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-600/25">
                        <LayoutDashboard class="w-5 h-5" />
                        <span class="font-bold">Dashboard</span>
                    </Link>

                    <Link href="/etudiants" class="group flex items-center gap-4 px-5 py-4 rounded-2xl text-slate-300 hover:text-white hover:bg-white/10 transition">
                        <GraduationCap class="w-5 h-5 text-blue-400" />
                        <span>Étudiants</span>
                    </Link>

                    <Link href="/cours" class="group flex items-center gap-4 px-5 py-4 rounded-2xl text-slate-300 hover:text-white hover:bg-white/10 transition">
                        <BookOpen class="w-5 h-5 text-purple-400" />
                        <span>Cours</span>
                    </Link>

                    <Link href="/chat" class="group flex items-center gap-4 px-5 py-4 rounded-2xl text-slate-300 hover:text-white hover:bg-white/10 transition">
                        <MessageCircle class="w-5 h-5 text-emerald-400" />
                        <span>Chat</span>
                    </Link>

                    <Link href="/teachers" class="group flex items-center gap-4 px-5 py-4 rounded-2xl text-slate-300 hover:text-white hover:bg-white/10 transition">
                        <Users class="w-5 h-5 text-orange-400" />
                        <span>Professeurs</span>
                    </Link>

                    <Link href="/profile" class="group flex items-center gap-4 px-5 py-4 rounded-2xl text-slate-300 hover:text-white hover:bg-white/10 transition">
                        <Settings class="w-5 h-5 text-pink-400" />
                        <span>Profil</span>
                    </Link>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-red-300 hover:text-red-200 hover:bg-red-500/10 transition"
                    >
                        <LogOut class="w-5 h-5" />
                        <span>Déconnexion</span>
                    </Link>
                </nav>
            </div>

            <div class="m-5 rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-slate-400">© 2026 Ecole Music</p>
            </div>
        </aside>

        <main class="flex-1 relative overflow-y-auto">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-32 right-10 w-96 h-96 bg-blue-600/20 blur-3xl rounded-full"></div>
                <div class="absolute top-60 -left-20 w-96 h-96 bg-purple-600/20 blur-3xl rounded-full"></div>
            </div>

            <header class="relative z-10 h-24 px-6 lg:px-10 flex items-center justify-between border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
                <div>
                    <div class="flex items-center gap-3">
                        <Sparkles class="w-6 h-6 text-blue-400" />
                        <h1 class="text-3xl font-black tracking-tight">
                            Tableau de bord
                        </h1>
                    </div>

                    <p class="text-slate-400 mt-1">
                        Bienvenue,
                        <span class="text-white font-semibold">
                            {{ user.name }}
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden xl:flex items-center gap-3 w-[340px] rounded-2xl border border-white/10 bg-white/5 px-5 py-3">
                        <Search class="w-5 h-5 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Rechercher étudiant, cours..."
                            class="w-full bg-transparent outline-none text-sm placeholder:text-slate-500"
                        />
                    </div>

                    <button class="relative w-13 h-13 p-4 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10 transition">
                        <Bell class="w-5 h-5" />
                        <span class="absolute top-3 right-3 w-3 h-3 bg-red-500 rounded-full ring-4 ring-slate-950"></span>
                    </button>


                </div>
            </header>

            <section class="relative z-10 p-6 lg:p-10">
                <div class="rounded-[34px] border border-white/10 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-emerald-600/10 p-8 shadow-2xl">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                        <div>
                            <p class="text-blue-300 font-semibold mb-2">
                                Administration musicale
                            </p>

                            <h2 class="text-4xl font-black tracking-tight">
                                Gérez votre école avec élégance
                            </h2>

                            <p class="text-slate-300 mt-3 max-w-2xl">
                                Suivi des étudiants, cours, professeurs, messages et activités depuis un seul espace professionnel.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <Link href="/etudiants" class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 font-bold transition">
                                Ajouter étudiant
                            </Link>

                            <Link href="/cours" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 font-bold transition">
                                Voir les cours
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-8">
                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-xl hover:-translate-y-1 transition">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center">
                                <GraduationCap class="w-6 h-6 text-blue-400" />
                            </div>
                            <TrendingUp class="w-5 h-5 text-emerald-400" />
                        </div>
                        <p class="text-slate-400 mt-6">Étudiants</p>
                        <h3 class="text-4xl font-black mt-1">250</h3>
                    </div>

                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-xl hover:-translate-y-1 transition">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/20 flex items-center justify-center">
                                <BookOpen class="w-6 h-6 text-purple-400" />
                            </div>
                            <TrendingUp class="w-5 h-5 text-emerald-400" />
                        </div>
                        <p class="text-slate-400 mt-6">Cours</p>
                        <h3 class="text-4xl font-black mt-1">35</h3>
                    </div>

                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-xl hover:-translate-y-1 transition">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                                <MessageCircle class="w-6 h-6 text-emerald-400" />
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300">
                                Live
                            </span>
                        </div>
                        <p class="text-slate-400 mt-6">Messages</p>
                        <h3 class="text-4xl font-black mt-1">120</h3>
                    </div>

                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-xl hover:-translate-y-1 transition">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-orange-500/20 flex items-center justify-center">
                                <Users class="w-6 h-6 text-orange-400" />
                            </div>
                            <CalendarDays class="w-5 h-5 text-slate-400" />
                        </div>
                        <p class="text-slate-400 mt-6">Professeurs</p>
                        <h3 class="text-4xl font-black mt-1">18</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mt-8">
                    <div class="xl:col-span-2 rounded-[32px] border border-white/10 bg-white/5 p-7 shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-black">Activités récentes</h2>
                                <p class="text-slate-400 text-sm">Dernières actions dans l’école</p>
                            </div>

                            <button class="px-5 py-2 rounded-2xl bg-blue-600 hover:bg-blue-700 font-semibold transition">
                                Voir plus
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-slate-950/50 p-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-purple-500/20 flex items-center justify-center">
                                        <BookOpen class="w-6 h-6 text-purple-400" />
                                    </div>

                                    <div>
                                        <h3 class="font-bold">Nouveau cours ajouté</h3>
                                        <p class="text-sm text-slate-400">Piano Moderne</p>
                                    </div>
                                </div>

                                <span class="text-sm text-blue-300">Aujourd’hui</span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-slate-950/50 p-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center">
                                        <GraduationCap class="w-6 h-6 text-blue-400" />
                                    </div>

                                    <div>
                                        <h3 class="font-bold">Utilisateur connecté</h3>
                                        <p class="text-sm text-slate-400">{{ user.name }}</p>
                                    </div>
                                </div>

                                <span class="text-sm text-emerald-300">Maintenant</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
