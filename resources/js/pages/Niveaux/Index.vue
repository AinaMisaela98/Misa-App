<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
    niveaux: { type: Array, default: () => [] },
})

const toast = ref('')
const toastColor = ref('blue')
const editingId = ref(null)

const form = useForm({ nom_niveau: '' })
const editForm = useForm({ nom_niveau: '' })

function showToast(message, color = 'blue') {
    toast.value = message
    toastColor.value = color
    setTimeout(() => toast.value = '', 3000)
}

function refresh() {
    router.reload({ only: ['niveaux'], preserveScroll: true })
}

function store() {
    form.clearErrors()

    if (!form.nom_niveau) {
        form.setError('nom_niveau', 'Le nom du niveau est obligatoire')
        showToast('⚠️ Nom du niveau obligatoire', 'red')
        return
    }

    form.post('/niveaux', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            showToast('✅ Niveau ajouté avec succès')
            refresh()
        },
        onError: () => showToast('❌ Ajout impossible', 'red'),
    })
}

function startEdit(niveau) {
    editingId.value = niveau.id
    editForm.nom_niveau = niveau.nom_niveau
}

function cancelEdit() {
    editingId.value = null
    editForm.reset()
    editForm.clearErrors()
}

function update(niveau) {
    editForm.clearErrors()

    if (!editForm.nom_niveau) {
        editForm.setError('nom_niveau', 'Le nom du niveau est obligatoire')
        return
    }

    editForm
        .transform(data => ({ ...data, _method: 'PUT' }))
        .post(`/niveaux/${niveau.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                editForm.transform(data => data)
                cancelEdit()
                showToast('✏️ Niveau modifié')
                refresh()
            },
            onError: () => showToast('❌ Modification impossible', 'red'),
        })
}

function destroy(niveau) {
    if (!confirm(`Supprimer le niveau "${niveau.nom_niveau}" ?`)) return

    router.post(`/niveaux/${niveau.id}`, { _method: 'DELETE' }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('🗑️ Niveau supprimé', 'red')
            refresh()
        },
    })
}
</script>

<template>
    <Head title="Gestion Niveaux" />

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
                    <h1 class="text-2xl font-black">🎓 Gestion des Niveaux</h1>
                    <p class="text-xs text-slate-500">Primaire, Collège, Lycée...</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="/classes" class="btn-dark">Classes</a>
                    <a href="/series" class="btn-dark">Séries</a>
                    <a href="/etudiants" class="btn-dark">Étudiants</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl p-5">
            <div class="mb-5 rounded-[28px] bg-white p-5 shadow-xl">
                <h2 class="mb-3 font-black">➕ Ajouter un niveau</h2>

                <div class="flex flex-col gap-3 md:flex-row">
                    <div class="flex-1">
                        <input
                            v-model="form.nom_niveau"
                            class="input-pro"
                            placeholder="Exemple : Primaire, Collège, Lycée"
                            @keyup.enter="store"
                        />
                        <p v-if="form.errors.nom_niveau" class="error">{{ form.errors.nom_niveau }}</p>
                    </div>

                    <button type="button" @click="store" :disabled="form.processing" class="btn-main">
                        {{ form.processing ? 'Ajout...' : 'Ajouter' }}
                    </button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="niveau in niveaux"
                    :key="niveau.id"
                    class="rounded-[26px] bg-white p-4 shadow-lg"
                >
                    <div v-if="editingId === niveau.id" class="space-y-3">
                        <input v-model="editForm.nom_niveau" class="input-pro" />
                        <p v-if="editForm.errors.nom_niveau" class="error">{{ editForm.errors.nom_niveau }}</p>

                        <div class="flex gap-2">
                            <button type="button" @click="update(niveau)" class="btn-save">Sauver</button>
                            <button type="button" @click="cancelEdit" class="btn-cancel">Annuler</button>
                        </div>
                    </div>

                    <div v-else class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-black">{{ niveau.nom_niveau }}</h3>
                            <p class="text-xs text-slate-500">{{ niveau.classes?.length || 0 }} classe(s)</p>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="startEdit(niveau)" class="mini amber">✏️</button>
                            <button type="button" @click="destroy(niveau)" class="mini red">🗑️</button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="niveaux.length === 0" class="rounded-3xl bg-white p-10 text-center text-slate-400">
                Aucun niveau créé.
            </div>
        </main>
    </div>
</template>

<style scoped>
.input-pro {
    width: 100%;
    height: 42px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 14px;
    outline: none;
}
.input-pro:focus {
    border-color: #14b8a6;
    background: white;
}
.btn-main {
    height: 42px;
    border-radius: 16px;
    background: #0d9488;
    padding: 0 20px;
    font-size: 13px;
    font-weight: 900;
    color: white;
}
.btn-dark {
    border-radius: 14px;
    background: #0f172a;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 900;
    color: white;
}
.btn-save {
    flex: 1;
    border-radius: 14px;
    background: #16a34a;
    padding: 10px;
    font-size: 12px;
    font-weight: 900;
    color: white;
}
.btn-cancel {
    flex: 1;
    border-radius: 14px;
    background: #e2e8f0;
    padding: 10px;
    font-size: 12px;
    font-weight: 900;
}
.mini {
    height: 34px;
    width: 34px;
    border-radius: 13px;
    font-size: 12px;
}
.mini.amber { background: #fef3c7; }
.mini.red { background: #ffe4e6; }
.error {
    margin-top: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #ef4444;
}
</style>
