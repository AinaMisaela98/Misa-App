<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

// ================== DATA ==================
const props = defineProps({
    etudiants: Array,
    filters: Object
});

// 🔥 INIT SHOW INFO (IMPORTANT)
onMounted(() => {
    props.etudiants.forEach(e => {
        e.show = false;
    });
});

// 🔍 SEARCH
const search = ref(props.filters.search || '');

// ➕ CREATE FORM
const form = useForm({
    nom: '',
    email: '',
    telephone: '',
    adresse: '',
    niveau: ''
   
});

// ✏️ EDIT
const showModal = ref(false);
const selected = ref(null);

const editForm = useForm({
    nom: '',
    email: '',
    telephone: '',
    adresse: '',
    niveau: ''
    
});

// 🔔 TOAST
const toast = ref('');

// ================== CREATE ==================
function submit() {
    form.post('/etudiants', {
        onSuccess: () => {
            form.reset();
            showToast('✅ Étudiant ajouté');
        }
    });
}

// ================== OPEN EDIT ==================
function openEdit(e) {
    selected.value = e;

    editForm.nom = e.nom;
    editForm.email = e.email;
    editForm.telephone = e.telephone;
    editForm.adresse = e.adresse;
    editForm.niveau = e.niveau;

    showModal.value = true;
}

// ================== UPDATE ==================
function update() {
    editForm.put(`/etudiants/${selected.value.id}`, {
        onSuccess: () => {
            showModal.value = false;
            showToast('✏️ Étudiant modifié');
        }
    });
}

// ================== DELETE ==================
function remove(id) {
    if (confirm('❌ Supprimer cet étudiant ?')) {
        router.delete(`/etudiants/${id}`, {
            onSuccess: () => showToast('🗑️ Étudiant supprimé')
        });
    }
}

// ================== SEARCH ==================
watch(search, (value) => {
    router.get('/etudiants', { search: value }, {
        preserveState: true,
        replace: true
    });
});

// ================== TOAST ==================
function showToast(msg) {
    toast.value = msg;
    setTimeout(() => toast.value = '', 2500);
}
</script>

<template>
<Head title="Étudiants" />

<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-800 text-white p-8">

    <!-- TOAST -->
    <div v-if="toast"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
        {{ toast }}
    </div>

    <!-- NAV PAR NIVEAU -->
    <div class="mb-6 flex gap-3">
        <a href="/etudiants/niveau/Débutant" class="bg-green-500 px-3 py-1 rounded text-black">
            Débutants
        </a>
        <a href="/etudiants/niveau/Intermédiaire" class="bg-yellow-500 px-3 py-1 rounded text-black">
            Intermédiaire
        </a>
        <a href="/etudiants/niveau/Avancé" class="bg-red-500 px-3 py-1 rounded text-black">
            Avancé
        </a>
    </div>

    <h1 class="text-3xl font-bold text-yellow-400 mb-6">
        🎓 Gestion Étudiants
    </h1>

    <!-- SEARCH -->
    <input
        v-model="search"
        type="text"
        placeholder="🔍 Rechercher..."
        class="w-full p-3 mb-6 rounded bg-gray-800 border border-gray-700"
    />

    <div class="grid grid-cols-3 gap-6">

        <!-- CREATE -->
        <div class="bg-gray-800 p-6 rounded-2xl shadow-lg">

            <h2 class="text-xl mb-4 text-yellow-400">➕ Ajouter</h2>

            <input v-model="form.nom" placeholder="Nom"
                class="w-full mb-2 p-2 bg-gray-700 rounded" />

            <input v-model="form.email" placeholder="Email"
                class="w-full mb-2 p-2 bg-gray-700 rounded" />

            <input v-model="form.telephone" placeholder="Téléphone"
                class="w-full mb-2 p-2 bg-gray-700 rounded" />
            <input v-model="form.adresse" placeholder="Adresse"
                class="w-full mb-2 p-2 bg-gray-700 rounded" />

            <select v-model="form.niveau"
                class="w-full mb-4 p-2 bg-gray-700 rounded">
                <option disabled value="">Choisir niveau</option>
                <option>Débutant</option>
                <option>Intermédiaire</option>
                <option>Avancé</option>
            </select>

            <button @click="submit"
                class="w-full bg-yellow-500 text-black py-2 rounded font-bold">
                💾 Ajouter
            </button>

        </div>

        <!-- LISTE -->
        <div class="col-span-2 bg-gray-800 p-6 rounded-2xl shadow-lg">

            <h2 class="text-xl mb-4 text-yellow-400">📋 Liste</h2>

            <table class="w-full text-left">

                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <tr  v-for="e in etudiants" :key="e.id"
                        class="border-b border-gray-700 hover:bg-gray-700">
                        <!-- NUMERO -->
                        <td class="py-2 text-yellow-400 font-bold">
                             {{ e.numero }}
                        </td>
                        <!-- NOM -->
                        <td class="py-2 font-semibold">
                            {{ e.nom }}
                        </td>
                        
                        <!-- INFO BUTTON -->
                        <td>
                            <button
                                @click="e.show = !e.show"
                                class="bg-blue-500 px-3 py-1 rounded text-white text-xs"
                            >
                                ℹ️ Info
                            </button>
                        </td>

                        <!-- ACTIONS -->
                        <td class="space-x-2">

                            <button @click="openEdit(e)"
                                class="bg-yellow-500 px-3 py-1 rounded text-black">
                                ✏️
                            </button>

                            <button @click="remove(e.id)"
                                class="bg-red-600 px-3 py-1 rounded">
                                🗑️
                            </button>

                        </td>

                    </tr>

                    <!-- DETAILS -->
                    <tr v-for="e in etudiants" :key="'info-'+e.id">

                        <td colspan="3" v-if="e.show"
                            class="bg-gray-900 p-4 text-sm text-gray-300">

                            📧 Email: {{ e.email }} <br>
                            📞 Téléphone: {{ e.telephone }} <br>
                            🏠 Adresse: {{ e.adresse }} <br>
                            🎓 Niveau:
                            <span class="text-red-400 font-bold">
                                {{ e.niveau }}
                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- MODAL -->
    <div v-if="showModal"
        class="fixed inset-0 bg-black/70 flex items-center justify-center">

        <div class="bg-gray-800 p-6 rounded-2xl w-96">

            <h2 class="text-xl mb-4 text-yellow-400">✏️ Modifier</h2>

            <input v-model="editForm.nom" class="w-full mb-2 p-2 bg-gray-700 rounded" />
            <input v-model="editForm.email" class="w-full mb-2 p-2 bg-gray-700 rounded" />
            <input v-model="editForm.telephone" class="w-full mb-2 p-2 bg-gray-700 rounded" />
            <input v-model="editForm.adresse" class="w-full mb-2 p-2 bg-gray-700 rounded" />

            <select v-model="editForm.niveau"
                class="w-full mb-4 p-2 bg-gray-700 rounded">
                <option>Débutant</option>
                <option>Intermédiaire</option>
                <option>Avancé</option>
            </select>

            <div class="flex justify-between">

                <button @click="showModal=false"
                    class="bg-gray-600 px-4 py-2 rounded">
                    Annuler
                </button>

                <button @click="update"
                    class="bg-green-500 px-4 py-2 rounded text-black font-bold">
                    Sauver
                </button>

            </div>

        </div>

    </div>

</div>
</template>