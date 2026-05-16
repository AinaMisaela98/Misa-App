<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    classes: { type: Array, default: () => [] },

    niveaux: { type: Array, default: () => [] },

    anneeActive: {
        type: String,
        default: '',
    },
})

const toast = ref('')
const toastColor = ref('blue')
const editingId = ref(null)
const search = ref('')

const filteredClasses = computed(() => {
    if (!search.value) return props.classes

    return props.classes.filter(classe =>
        classe.nom_classe?.toLowerCase().includes(search.value.toLowerCase()) ||
        classe.niveau?.nom_niveau?.toLowerCase().includes(search.value.toLowerCase()) ||
        classe.annee_scolaire?.toLowerCase().includes(search.value.toLowerCase())
    )
})

const form = useForm({
    niveau_id: '',
    nom_classe: '',
})

const editForm = useForm({
    niveau_id: '',
    nom_classe: '',
})

function showToast(message, color = 'blue') {
    toast.value = message
    toastColor.value = color
    setTimeout(() => toast.value = '', 3000)
}

function refresh() {
    router.reload({
        only: ['classes', 'niveaux', 'anneeActive'],
        preserveScroll: true,
        preserveState: true,
    })
}

function store() {
    form.clearErrors()

    if (!form.niveau_id || !form.nom_classe) {
        if (!form.niveau_id) form.setError('niveau_id', 'Le niveau est obligatoire')
        if (!form.nom_classe) form.setError('nom_classe', 'La classe est obligatoire')
        showToast('⚠️ Choisissez un niveau et entrez une classe', 'red')
        return
    }

    form.post('/classes', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset()
            showToast('✅ Classe ajoutée avec succès', 'blue')
            refresh()
        },
        onError: () => {
            showToast('❌ Ajout impossible. Vérifiez le niveau choisi.', 'red')
        },
    })
}

function startEdit(classe) {
    editingId.value = classe.id
    editForm.clearErrors()
    editForm.niveau_id = classe.niveau_id ? String(classe.niveau_id) : ''
    editForm.nom_classe = classe.nom_classe || ''
}

function cancelEdit() {
    editingId.value = null
    editForm.reset()
    editForm.clearErrors()
}

function update(classe) {
    editForm.clearErrors()

    if (!editForm.niveau_id || !editForm.nom_classe) {
        if (!editForm.niveau_id) editForm.setError('niveau_id', 'Le niveau est obligatoire')
        if (!editForm.nom_classe) editForm.setError('nom_classe', 'La classe est obligatoire')
        showToast('⚠️ Information incomplète', 'red')
        return
    }

    editForm
        .transform(data => ({
            ...data,
            _method: 'PUT',
        }))
        .post(`/classes/${classe.id}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                editForm.transform(data => data)
                cancelEdit()
                showToast('✏️ Classe modifiée avec succès', 'blue')
                refresh()
            },
            onError: () => {
                showToast('❌ Modification impossible', 'red')
            },
        })
}

function destroy(classe) {
    if (!confirm(`Voulez-vous vraiment supprimer la classe "${classe.nom_classe}" ?`)) return

    router.post(`/classes/${classe.id}`, { _method: 'DELETE' }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showToast('🗑️ Classe supprimée', 'red')
            refresh()
        },
    })
}
</script>

<template>
    <Head title="Gestion Classes" />

    <div class="min-h-screen bg-slate-100 text-slate-900">
        <div
            v-if="toast"
            :class="[
                'fixed right-4 top-4 z-50 rounded-2xl px-5 py-3 text-sm font-black text-white shadow-2xl',
                toastColor === 'blue' ? 'bg-blue-600' : 'bg-red-600'
            ]"
        >
            {{ toast }}
        </div>

        <header class="border-b bg-white px-5 py-4 shadow-sm">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-black">🏫 Gestion des Classes</h1>
                    <p class="text-xs text-slate-500">
                        Créer une classe et l’associer à un niveau.
                    </p>

                    <div class="mt-3 inline-flex rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">
                        📅 Année scolaire active :
                        {{ anneeActive || 'Non définie' }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="/niveaux" class="btn-dark">Niveaux</a>
                    <a href="/series" class="btn-dark">Séries</a>
                    <a href="/etudiants" class="btn-dark">Étudiants</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl p-5">
            <div
                v-if="niveaux.length === 0"
                class="mb-5 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700"
            >
                ⚠️ Aucun niveau trouvé. Créez d’abord un niveau dans la page
                <a href="/niveaux" class="underline">Niveaux</a>.
            </div>

            <div
                v-if="!anneeActive"
                class="mb-5 rounded-3xl border border-orange-200 bg-orange-50 p-4 text-sm font-bold text-orange-700"
            >
                ⚠️ Aucune année scolaire active. Activez d’abord une année scolaire dans Paramètres.
            </div>

            <div class="mb-5 rounded-[28px] bg-white p-5 shadow-xl">
                <h2 class="mb-3 font-black">➕ Ajouter une classe</h2>

                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <select v-model="form.niveau_id" class="input-pro">
                            <option disabled value="">Choisir niveau</option>
                            <option
                                v-for="niveau in niveaux"
                                :key="niveau.id"
                                :value="String(niveau.id)"
                            >
                                {{ niveau.nom_niveau }}
                            </option>
                        </select>
                        <p v-if="form.errors.niveau_id" class="error">
                            {{ form.errors.niveau_id }}
                        </p>
                    </div>

                    <div>
                        <input
                            v-model="form.nom_classe"
                            class="input-pro"
                            placeholder="Exemple : Grade 1, 6ème..."
                            @keyup.enter="store"
                        />
                        <p v-if="form.errors.nom_classe" class="error">
                            {{ form.errors.nom_classe }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="store"
                        :disabled="form.processing || niveaux.length === 0 || !anneeActive"
                        class="btn-main"
                    >
                        {{ form.processing ? 'Ajout...' : 'Ajouter classe' }}
                    </button>
                </div>
            </div>

            <div class="mb-4 rounded-2xl bg-white p-3 shadow-sm">
                <input
                    v-model="search"
                    class="input-pro"
                    placeholder="Rechercher par classe, niveau ou année scolaire..."
                />
            </div>

            <div class="overflow-hidden rounded-[28px] bg-white shadow-xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Classe</th>
                            <th class="px-4 py-3">Niveau</th>
                            <th class="px-4 py-3">Année scolaire</th>
                            <th class="px-4 py-3">Séries</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="classe in filteredClasses"
                            :key="classe.id"
                            class="border-t border-slate-100 hover:bg-slate-50"
                        >
                            <template v-if="editingId === classe.id">
                                <td class="px-4 py-3">
                                    <input v-model="editForm.nom_classe" class="input-pro" />
                                    <p v-if="editForm.errors.nom_classe" class="error">
                                        {{ editForm.errors.nom_classe }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <select v-model="editForm.niveau_id" class="input-pro">
                                        <option disabled value="">Choisir niveau</option>
                                        <option
                                            v-for="niveau in niveaux"
                                            :key="niveau.id"
                                            :value="String(niveau.id)"
                                        >
                                            {{ niveau.nom_niveau }}
                                        </option>
                                    </select>
                                    <p v-if="editForm.errors.niveau_id" class="error">
                                        {{ editForm.errors.niveau_id }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                        {{ classe.annee_scolaire || anneeActive || '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ classe.series?.length || 0 }} série(s)
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="update(classe)" class="mini green">✓</button>
                                    <button type="button" @click="cancelEdit" class="mini slate">✕</button>
                                </td>
                            </template>

                            <template v-else>
                                <td class="px-4 py-3 font-black">
                                    {{ classe.nom_classe }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-teal-50 px-3 py-1 text-xs font-black text-teal-700">
                                        {{ classe.niveau?.nom_niveau || 'Non défini' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                        {{ classe.annee_scolaire || anneeActive || '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ classe.series?.length || 0 }} série(s)
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="startEdit(classe)" class="mini amber">✏️</button>
                                    <button type="button" @click="destroy(classe)" class="mini red">🗑️</button>
                                </td>
                            </template>
                        </tr>

                        <tr v-if="filteredClasses.length === 0">
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Aucune classe trouvée pour cette année scolaire.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>

<style scoped>
.input-pro {
    width: 100%;
    height: 40px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 12px;
    outline: none;
    font-size: 13px;
}

.input-pro:focus {
    border-color: #14b8a6;
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.12);
}

.btn-main {
    height: 40px;
    border-radius: 14px;
    background: #0d9488;
    font-size: 13px;
    font-weight: 900;
    color: white;
}

.btn-main:hover {
    background: #0f766e;
}

.btn-main:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-dark {
    border-radius: 14px;
    background: #0f172a;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 900;
    color: white;
}

.btn-dark:hover {
    background: #1e293b;
}

.mini {
    margin-left: 4px;
    height: 32px;
    width: 32px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 900;
}

.mini.green {
    background: #dcfce7;
    color: #15803d;
}

.mini.slate {
    background: #e2e8f0;
    color: #334155;
}

.mini.amber {
    background: #fef3c7;
    color: #b45309;
}

.mini.red {
    background: #ffe4e6;
    color: #be123c;
}

.error {
    margin-top: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #ef4444;
}
</style>
