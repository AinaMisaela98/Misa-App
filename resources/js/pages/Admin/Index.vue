<script setup>
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CreateUser from './Users/Create.vue'
import EditUser from './Users/Edit.vue'

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
})

const page = usePage()

const showCreate = ref(false)
const showEdit = ref(false)
const selectedUser = ref(null)

const totalUsers = computed(() => props.users.length)
const totalAdmins = computed(() => props.users.filter(user => user.role === 'admin').length)
const totalUsersSimple = computed(() => props.users.filter(user => user.role === 'user').length)

const openCreate = () => {
    showCreate.value = true
    showEdit.value = false
}

const openEdit = (user) => {
    selectedUser.value = user
    showEdit.value = true
    showCreate.value = false
}

const closeForms = () => {
    showCreate.value = false
    showEdit.value = false
    selectedUser.value = null
}

const deleteUser = (user) => {
    if (confirm(`Supprimer ${user.name} ?`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true
        })
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-100">
        <div class="flex min-h-screen">

            <!-- SIDEBAR -->
            <aside class="hidden lg:flex w-72 flex-col bg-slate-950 text-white p-6">
                <div class="mb-10">
                    <div class="h-14 w-14 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl font-black">
                        A
                    </div>
                    <h1 class="mt-4 text-3xl font-black">Admin Pro</h1>
                    <p class="text-slate-400 text-sm">Gestion intelligente</p>
                </div>

                <nav class="space-y-3">
                    <button class="w-full text-left rounded-2xl bg-blue-600 px-5 py-3 font-bold shadow">
                        Dashboard
                    </button>

                    <button
                        @click="openCreate"
                        class="w-full text-left rounded-2xl px-5 py-3 font-bold hover:bg-slate-800"
                    >
                        + Créer utilisateur
                    </button>

                    <a
                        href="/dashboard"
                        class="block rounded-2xl px-5 py-3 font-bold hover:bg-slate-800"
                    >
                        Retour dashboard
                    </a>
                </nav>
            </aside>

            <!-- MAIN -->
            <main class="flex-1 p-5 lg:p-10">

                <!-- HEADER -->
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900">
                            Gestion des utilisateurs
                        </h2>
                        <p class="text-slate-500 mt-1">
                            Créer, modifier et supprimer les comptes en temps réel
                        </p>
                    </div>

                    <button
                        @click="openCreate"
                        class="rounded-2xl bg-blue-600 px-6 py-3 text-white font-bold shadow hover:bg-blue-700"
                    >
                        + Nouveau compte
                    </button>
                </div>

                <!-- MESSAGE -->
                <div
                    v-if="page.props.flash?.success"
                    class="mb-6 rounded-2xl bg-green-100 px-5 py-4 text-green-700 font-bold"
                >
                    {{ page.props.flash.success }}
                </div>

                <!-- STATS -->
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

                <!-- FORMULAIRE CREATE / EDIT -->
                <div v-if="showCreate || showEdit" class="mb-8">
                    <CreateUser
                        v-if="showCreate"
                        @cancel="closeForms"
                        @saved="closeForms"
                    />

                    <EditUser
                        v-if="showEdit && selectedUser"
                        :user="selectedUser"
                        @cancel="closeForms"
                        @saved="closeForms"
                    />
                </div>

                <!-- TABLE -->
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

                                    <td class="p-4 text-right space-x-2">
                                        <button
                                            @click="openEdit(user)"
                                            class="rounded-xl bg-amber-400 px-4 py-2 text-white font-bold hover:bg-amber-500"
                                        >
                                            Modifier
                                        </button>

                                        <button

                                                     @click="deleteUser(user)"
                                                     class="rounded-xl bg-red-500 px-4 py-2 text-white font-bold hover:bg-red-600"
                                        >
                                                       Supprimer

                                        </button>
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
