<script setup>
import { computed, ref } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const showForm = ref(false)
const editingUser = ref(null)
const notification = ref(null)

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
})

const totalUsers = computed(() => props.users.length)
const totalAdmins = computed(() => props.users.filter(user => user.role === 'admin').length)
const totalUsersSimple = computed(() => props.users.filter(user => user.role === 'user').length)

function showNotification(message, type = 'success') {
    notification.value = {
        message,
        type,
    }

    setTimeout(() => {
        notification.value = null
    }, 3000)
}

function openCreate() {
    showForm.value = true
    editingUser.value = null
    form.reset()
    form.clearErrors()
    form.role = 'user'
}

function openEdit(user) {
    if (!user?.id) return

    showForm.value = true
    editingUser.value = user
    form.clearErrors()

    form.name = user.name ?? ''
    form.email = user.email ?? ''
    form.password = ''
    form.role = user.role ?? 'user'
}

function closeForm() {
    showForm.value = false
    editingUser.value = null
    form.reset()
    form.clearErrors()
    form.role = 'user'
}

function submit() {
    if (editingUser.value?.id) {
        form
            .transform((data) => ({
                ...data,
                _method: 'PUT',
            }))
            .post(`/admin/users/${editingUser.value.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    form.transform((data) => data)
                    closeForm()
                    showNotification('Utilisateur modifié avec succès')
                },
            })

        return
    }

    form
        .transform((data) => {
            const { _method, ...cleanData } = data
            return cleanData
        })
        .post('/admin/users', {
            preserveScroll: true,
            onSuccess: () => {
                closeForm()
                showNotification('Utilisateur créé avec succès')
            },
        })
}

function deleteUser(user) {
    if (!user?.id) return

    if (confirm(`Voulez-vous vraiment supprimer ${user.name} ?`)) {
        router.post(
            `/admin/users/${user.id}`,
            { _method: 'DELETE' },
            {
                preserveScroll: true,
                onSuccess: () => {
                    showNotification('Utilisateur supprimé avec succès')
                },
            }
        )
    }
}
</script>

<template>
    <Head title="Administration" />

    <div class="min-h-screen bg-slate-100 relative">

        <!-- NOTIFICATION -->
        <div
            v-if="notification"
            class="fixed top-6 right-6 z-50 rounded-2xl px-6 py-4 font-bold shadow-2xl transition"
            :class="notification.type === 'success'
                ? 'bg-green-500 text-white'
                : 'bg-red-500 text-white'"
        >
            ✅ {{ notification.message }}
        </div>

        <div class="flex min-h-screen">
            <aside class="hidden lg:flex w-72 flex-col bg-slate-950 text-white p-6">
                <div class="mb-10">
                    <div class="h-14 w-14 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl font-black">
                        A
                    </div>

                    <h1 class="mt-4 text-3xl font-black">Admin Pro</h1>
                    <p class="text-slate-400 text-sm">Gestion intelligente</p>
                </div>

                <nav class="space-y-3">
                    <button
                        type="button"
                        class="w-full text-left rounded-2xl bg-blue-600 px-5 py-3 font-bold shadow"
                    >
                        Dashboard
                    </button>

                    <button
                        type="button"
                        @click="openCreate"
                        class="w-full text-left rounded-2xl px-5 py-3 font-bold hover:bg-slate-800"
                    >
                        + Créer utilisateur
                    </button>

                    <a href="/dashboard" class="block rounded-2xl px-5 py-3 font-bold hover:bg-slate-800">
                        Retour dashboard
                    </a>
                </nav>
            </aside>

            <main class="flex-1 p-5 lg:p-10">
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900">
                            Gestion des utilisateurs
                        </h2>

                        <p class="text-slate-500 mt-1">
                            Créer, modifier et supprimer les comptes.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate"
                        class="rounded-2xl bg-blue-600 px-6 py-3 text-white font-bold shadow hover:bg-blue-700"
                    >
                        + Nouveau compte
                    </button>
                </div>

                <div
                    v-if="page.props.flash?.success"
                    class="mb-6 rounded-2xl bg-green-100 px-5 py-4 text-green-700 font-bold"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <div class="rounded-3xl bg-white p-6 shadow">
                        <p class="text-slate-500 font-semibold">Total</p>
                        <h3 class="text-4xl font-black mt-2">{{ totalUsers }}</h3>
                    </div>

                    <div class="rounded-3xl bg-white p-6 shadow">
                        <p class="text-slate-500 font-semibold">Administrateurs</p>
                        <h3 class="text-4xl font-black mt-2 text-blue-600">{{ totalAdmins }}</h3>
                    </div>

                    <div class="rounded-3xl bg-white p-6 shadow">
                        <p class="text-slate-500 font-semibold">Utilisateurs</p>
                        <h3 class="text-4xl font-black mt-2 text-emerald-600">{{ totalUsersSimple }}</h3>
                    </div>
                </div>

                <form
                    v-if="showForm"
                    @submit.prevent="submit"
                    class="mb-8 rounded-3xl bg-white p-6 shadow"
                >
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ editingUser ? 'Modifier utilisateur' : 'Créer utilisateur' }}
                            </h3>

                            <p class="text-slate-500 text-sm">
                                {{ editingUser ? 'Modification de : ' + editingUser.name : 'Ajouter un nouveau compte.' }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="closeForm"
                            class="rounded-xl bg-slate-200 px-4 py-2 font-bold text-slate-700 hover:bg-slate-300"
                        >
                            Annuler
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="font-bold text-slate-700">Nom</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-bold text-slate-700">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-bold text-slate-700">Mot de passe</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500"
                                :placeholder="editingUser ? 'Laisser vide si inchangé' : 'Mot de passe'"
                            />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div>
                            <label class="font-bold text-slate-700">Rôle</label>
                            <select
                                v-model="form.role"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500"
                            >
                                <option value="user">user</option>
                                <option value="admin">admin</option>
                            </select>
                            <InputError :message="form.errors.role" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-2xl bg-blue-600 px-6 py-3 text-white font-bold shadow hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Traitement...' : (editingUser ? 'Sauver modification' : 'Créer utilisateur') }}
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-3xl bg-white shadow">
                    <div class="border-b px-6 py-5 flex justify-between items-center">
                        <h3 class="text-xl font-black text-slate-900">
                            Liste des comptes
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[750px]">
                            <thead class="bg-slate-950 text-white">
                                <tr>
                                    <th class="p-4 text-left">Nom</th>
                                    <th class="p-4 text-left">Email</th>
                                    <th class="p-4 text-left">Rôle</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="border-b hover:bg-slate-50"
                                >
                                    <td class="p-4 font-bold">{{ user.name }}</td>
                                    <td class="p-4 text-slate-600">{{ user.email }}</td>

                                    <td class="p-4">
                                        <span
                                            class="rounded-full px-4 py-1 text-sm font-bold"
                                            :class="user.role === 'admin'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-slate-100 text-slate-700'"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="openEdit(user)"
                                                class="rounded-xl bg-amber-400 px-4 py-2 text-white font-bold hover:bg-amber-500"
                                            >
                                                Modifier
                                            </button>

                                            <button
                                                type="button"
                                                @click="deleteUser(user)"
                                                class="rounded-xl bg-red-500 px-4 py-2 text-white font-bold hover:bg-red-600"
                                            >
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="users.length === 0">
                                    <td colspan="4" class="p-10 text-center text-slate-500 font-bold">
                                        Aucun utilisateur trouvé.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
