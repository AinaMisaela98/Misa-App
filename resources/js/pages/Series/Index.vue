<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    series: { type: Array, default: () => [] },

    classes: { type: Array, default: () => [] },

    anneeActive: {
        type: String,
        default: '',
    },
})

const toast = ref('')
const toastColor = ref('blue')
const editingId = ref(null)
const selectedClasseFilter = ref('')
const search = ref('')

const filteredSeries = computed(() => {
    let list = props.series

    if (selectedClasseFilter.value) {
        list = list.filter(s => Number(s.classe_id) === Number(selectedClasseFilter.value))
    }

    if (search.value) {
        list = list.filter(s =>
            s.nom_serie?.toLowerCase().includes(search.value.toLowerCase()) ||
            s.classe?.nom_classe?.toLowerCase().includes(search.value.toLowerCase()) ||
            s.classe?.niveau?.nom_niveau?.toLowerCase().includes(search.value.toLowerCase()) ||
            s.annee_scolaire?.toLowerCase().includes(search.value.toLowerCase())
        )
    }

    return list
})

const form = useForm({
    classe_id: '',
    nom_serie: '',
})

const editForm = useForm({
    classe_id: '',
    nom_serie: '',
})

function showToast(message, color = 'blue') {
    toast.value = message
    toastColor.value = color
    setTimeout(() => toast.value = '', 3000)
}

function refresh() {
    router.reload({
        only: ['series', 'classes', 'anneeActive'],
        preserveScroll: true,
        preserveState: true,
    })
}

function store() {
    form.clearErrors()

    if (!form.classe_id || !form.nom_serie) {
        if (!form.classe_id) form.setError('classe_id', 'La classe est obligatoire')
        if (!form.nom_serie) form.setError('nom_serie', 'La série est obligatoire')
        showToast('⚠️ Information incomplète', 'red')
        return
    }

    form.post('/series', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset()
            showToast('✅ Série ajoutée')
            refresh()
        },
        onError: () => showToast('❌ Ajout impossible', 'red'),
    })
}

function startEdit(serie) {
    editingId.value = serie.id
    editForm.clearErrors()
    editForm.classe_id = serie.classe_id ? String(serie.classe_id) : ''
    editForm.nom_serie = serie.nom_serie || ''
}

function cancelEdit() {
    editingId.value = null
    editForm.reset()
    editForm.clearErrors()
}

function update(serie) {
    editForm.clearErrors()

    if (!editForm.classe_id || !editForm.nom_serie) {
        if (!editForm.classe_id) editForm.setError('classe_id', 'La classe est obligatoire')
        if (!editForm.nom_serie) editForm.setError('nom_serie', 'La série est obligatoire')
        showToast('⚠️ Information incomplète', 'red')
        return
    }

    editForm
        .transform(data => ({
            ...data,
            _method: 'PUT',
        }))
        .post(`/series/${serie.id}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                editForm.transform(data => data)
                cancelEdit()
                showToast('✏️ Série modifiée')
                refresh()
            },
            onError: () => showToast('❌ Modification impossible', 'red'),
        })
}

function destroy(serie) {
    if (!confirm(`Supprimer la série "${serie.nom_serie}" ?`)) return

    router.post(`/series/${serie.id}`, { _method: 'DELETE' }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showToast('🗑️ Série supprimée', 'red')
            refresh()
        },
    })
}
</script>

<template>
    <Head title="Gestion Séries" />

    <div class="min-h-screen bg-slate-100">
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
                    <h1 class="text-2xl font-black">🔠 Gestion des Séries</h1>
                    <p class="text-xs text-slate-500">
                        Créer A, B, C dans une classe selon l’année scolaire active.
                    </p>

                    <div class="mt-3 inline-flex rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700">
                        📅 Année scolaire active :
                        {{ anneeActive || 'Non définie' }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="/niveaux" class="btn-dark">Niveaux</a>
                    <a href="/classes" class="btn-dark">Classes</a>
                    <a href="/etudiants" class="btn-dark">Étudiants</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl p-5">
            <div
                v-if="!anneeActive"
                class="mb-5 rounded-3xl border border-orange-200 bg-orange-50 p-4 text-sm font-bold text-orange-700"
            >
                ⚠️ Aucune année scolaire active. Activez d’abord une année scolaire dans Paramètres.
            </div>

            <div
                v-if="classes.length === 0"
                class="mb-5 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700"
            >
                ⚠️ Aucune classe trouvée pour cette année scolaire. Créez d’abord une classe dans la page
                <a href="/classes" class="underline">Classes</a>.
            </div>

            <div class="mb-5 rounded-[28px] bg-white p-5 shadow-xl">
                <h2 class="mb-3 font-black">➕ Ajouter une série</h2>

                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <select v-model="form.classe_id" class="input-pro">
                            <option disabled value="">Choisir classe</option>
                            <option
                                v-for="classe in classes"
                                :key="classe.id"
                                :value="String(classe.id)"
                            >
                                {{ classe.nom_classe }} - {{ classe.niveau?.nom_niveau || 'Sans niveau' }}
                            </option>
                        </select>
                        <p v-if="form.errors.classe_id" class="error">
                            {{ form.errors.classe_id }}
                        </p>
                    </div>

                    <div>
                        <input
                            v-model="form.nom_serie"
                            class="input-pro"
                            placeholder="Exemple : A, B, C"
                            @keyup.enter="store"
                        />
                        <p v-if="form.errors.nom_serie" class="error">
                            {{ form.errors.nom_serie }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="store"
                        :disabled="form.processing || classes.length === 0 || !anneeActive"
                        class="btn-main"
                    >
                        {{ form.processing ? 'Ajout...' : 'Ajouter série' }}
                    </button>
                </div>
            </div>

            <div class="mb-4 grid gap-3 rounded-2xl bg-white p-3 shadow-sm md:grid-cols-2">
                <select v-model="selectedClasseFilter" class="input-pro">
                    <option value="">Toutes les classes</option>
                    <option
                        v-for="classe in classes"
                        :key="classe.id"
                        :value="String(classe.id)"
                    >
                        {{ classe.nom_classe }}
                    </option>
                </select>

                <input
                    v-model="search"
                    class="input-pro"
                    placeholder="Rechercher par série, classe, niveau ou année scolaire..."
                />
            </div>

            <div class="overflow-hidden rounded-[28px] bg-white shadow-xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Série</th>
                            <th class="px-4 py-3">Classe</th>
                            <th class="px-4 py-3">Niveau</th>
                            <th class="px-4 py-3">Année scolaire</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="serie in filteredSeries"
                            :key="serie.id"
                            class="border-t border-slate-100 hover:bg-slate-50"
                        >
                            <template v-if="editingId === serie.id">
                                <td class="px-4 py-3">
                                    <input v-model="editForm.nom_serie" class="input-pro" />
                                    <p v-if="editForm.errors.nom_serie" class="error">
                                        {{ editForm.errors.nom_serie }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <select v-model="editForm.classe_id" class="input-pro">
                                        <option value="">Choisir classe</option>
                                        <option
                                            v-for="classe in classes"
                                            :key="classe.id"
                                            :value="String(classe.id)"
                                        >
                                            {{ classe.nom_classe }}
                                        </option>
                                    </select>
                                    <p v-if="editForm.errors.classe_id" class="error">
                                        {{ editForm.errors.classe_id }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    {{ serie.classe?.niveau?.nom_niveau || '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                        {{ serie.annee_scolaire || anneeActive || '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="update(serie)" class="mini green">✓</button>
                                    <button type="button" @click="cancelEdit" class="mini slate">✕</button>
                                </td>
                            </template>

                            <template v-else>
                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-teal-50 px-3 py-1 font-black text-teal-700">
                                        Série {{ serie.nom_serie }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 font-bold">
                                    {{ serie.classe?.nom_classe || 'Non défini' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ serie.classe?.niveau?.nom_niveau || '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-xl bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                        {{ serie.annee_scolaire || anneeActive || '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="startEdit(serie)" class="mini amber">✏️</button>
                                    <button type="button" @click="destroy(serie)" class="mini red">🗑️</button>
                                </td>
                            </template>
                        </tr>

                        <tr v-if="filteredSeries.length === 0">
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Aucune série trouvée pour cette année scolaire.
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
