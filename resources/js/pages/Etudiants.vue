<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import InputError from '@/Components/InputError.vue'

// PROPS
const props = defineProps({
    etudiants: Array,
    filters: Object,
})

// SEARCH
const search = ref(props.filters?.search || '')

// SELECTED INFO
const selectedEtudiant = ref(null)

// FORM
const form = useForm({
    nom: '',
    email: '',
    telephone: '',
    adresse: '',
    niveau: '',
})

// AJOUT
function submit() {
    form.post('/etudiants', {
        onSuccess: () => form.reset()
    })
}

// SEARCH LIVE
watch(search, (value) => {
    router.get('/etudiants', { search: value }, {
        preserveState: true,
        replace: true,
    })
})

// SHOW INFO
function showInfo(etudiant) {
    selectedEtudiant.value = etudiant
}

// CLOSE MODAL
function closeInfo() {
    selectedEtudiant.value = null
}
</script>

<template>
<Head title="Étudiants" />

<div class="min-h-screen bg-black text-white p-6">

    <!-- HEADER -->
    <h1 class="text-3xl font-bold text-yellow-400 mb-6">
        👨‍🎓 Gestion Étudiants
    </h1>

    <!-- SEARCH -->
    <input
        v-model="search"
        placeholder="🔍 Rechercher..."
        class="w-full mb-6 p-3 rounded bg-gray-800 border border-gray-700"
    />

    <!-- TABLE -->
    <div class="bg-gray-900 rounded-xl p-4">

        <table class="w-full text-left">

            <thead>
                <tr class="border-b border-gray-700">
                    <th>N°</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <tr
                    v-for="e in etudiants"
                    :key="e.id"
                    class="border-b border-gray-800 hover:bg-gray-800"
                >

                    <td class="py-2 text-yellow-400">
                        {{ e.numero }}
                    </td>

                    <td class="font-semibold">
                        {{ e.nom }}
                    </td>

                    <td>

                        <button
                            @click="showInfo(e)"
                            class="bg-blue-500 px-3 py-1 rounded hover:bg-blue-600"
                        >
                            ℹ️ Info
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <!-- ⭐ CENTER MODAL INFO -->
    <div
        v-if="selectedEtudiant"
        class="fixed inset-0 flex items-center justify-center bg-black/70 z-50"
    >

        <div class="bg-gray-900 w-full max-w-md rounded-2xl p-6 shadow-2xl border border-gray-700">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-4">

                <h2 class="text-xl font-bold text-yellow-400">
                    🎓 Information Étudiant
                </h2>

                <button
                    @click="closeInfo"
                    class="text-red-400 text-xl"
                >
                    ✖
                </button>

            </div>

            <!-- CONTENT -->
            <div class="space-y-3 text-gray-200">

                <p><b>Nom :</b> {{ selectedEtudiant.nom }}</p>
                <p><b>Email :</b> {{ selectedEtudiant.email }}</p>
                <p><b>Téléphone :</b> {{ selectedEtudiant.telephone }}</p>
                <p><b>Adresse :</b> {{ selectedEtudiant.adresse }}</p>
                <p><b>Niveau :</b> {{ selectedEtudiant.niveau }}</p>

            </div>

        </div>

    </div>

</div>
</template>