<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    etudiants: { type: Array, default: () => [] },
    classes: { type: Array, default: () => [] },
    anneeScolaires: { type: Array, default: () => [] },
    reinscriptions: { type: Array, default: () => [] },
})

const searchEtudiant = ref('')
const showReinscriptions = ref(false)
const affichageVide = ref(false)
const localReinscriptions = ref([])

const toast = ref('')
const toastColor = ref('red')

const form = useForm({
    etudiant_id: '',
    annee_scolaire: '',
    classe_id: '',
    serie_id: '',
})

const filteredEtudiants = computed(() => {
    const keyword = searchEtudiant.value.toLowerCase().trim()

    if (!keyword) return props.etudiants

    return props.etudiants.filter(e =>
        String(e.numero || '').toLowerCase().includes(keyword) ||
        String(e.nom || '').toLowerCase().includes(keyword) ||
        String(e.prenoms || '').toLowerCase().includes(keyword)
    )
})

const selectedEtudiant = computed(() => {
    return props.etudiants.find(e => Number(e.id) === Number(form.etudiant_id))
})

const selectedClasse = computed(() => {
    return props.classes.find(c => Number(c.id) === Number(form.classe_id))
})

const seriesDisponibles = computed(() => {
    return selectedClasse.value?.series || []
})

watch(() => form.classe_id, () => {
    form.serie_id = ''
})

function showToast(message, color = 'red') {
    toast.value = message
    toastColor.value = color

    setTimeout(() => {
        toast.value = ''
    }, 4000)
}

function viderAffichage() {
    if (!confirm('Voulez-vous vraiment supprimer définitivement tout l’historique ?')) {
        return
    }

    router.delete('/reinscriptions/historique/delete', {
        preserveScroll: true,

        onSuccess: () => {
            localReinscriptions.value = []
            affichageVide.value = true
            showReinscriptions.value = true
            showToast('✅ Historique supprimé définitivement.', 'green')
        },

        onError: () => {
            showToast('❌ Suppression impossible.', 'red')
        },
    })
}

function submit() {
    form.clearErrors()

    const etudiant = props.etudiants.find(e => Number(e.id) === Number(form.etudiant_id))

    if (!etudiant) {
        showToast('❌ Veuillez sélectionner un étudiant.', 'red')
        return
    }

    if (!form.annee_scolaire) {
        showToast('❌ Veuillez sélectionner une année scolaire.', 'red')
        return
    }

    const doublonMatricule = props.etudiants.some(e =>
        String(e.numero || '').trim() === String(etudiant.numero || '').trim()
        && String(e.annee_scolaire || '').trim() === String(form.annee_scolaire || '').trim()
    )

    if (doublonMatricule) {
        showToast('❌ Déjà inscrit pour cette année scolaire.', 'red')
        form.setError('etudiant_id', 'Déjà inscrit pour cette année scolaire.')
        return
    }

    form.post('/reinscriptions', {
        preserveScroll: true,

        onSuccess: (page) => {
            form.reset()
            searchEtudiant.value = ''
            showReinscriptions.value = true

            showToast('✅ Réinscription effectuée avec succès.', 'green')

            const allReinscriptions = page.props.reinscriptions || props.reinscriptions || []

            localReinscriptions.value = allReinscriptions.length
                ? [allReinscriptions[0]]
                : []

            affichageVide.value = false
        },

        onError: (errors) => {
            if (errors.etudiant_id) {
                showToast('❌ ' + errors.etudiant_id, 'red')
                return
            }

            showToast('❌ Réinscription impossible.', 'red')
        },
    })
}
</script>

<template>
    <Head title="Réinscriptions" />

    <div class="min-h-screen bg-slate-100 p-3 text-slate-900 md:p-5">
        <div
            v-if="toast"
            :class="[
                'fixed right-4 top-4 z-[999] max-w-[92vw] rounded-2xl px-5 py-3 text-sm font-black text-white shadow-2xl',
                toastColor === 'green' ? 'bg-emerald-600' : 'bg-red-600'
            ]"
        >
            {{ toast }}
        </div>

        <div class="mb-5">
            <h1 class="text-xl font-black md:text-2xl">🔁 Réinscription</h1>
            <p class="text-xs text-slate-500 md:text-sm">
                Réinscrire un étudiant existant dans une nouvelle année scolaire.
            </p>
        </div>

        <div class="mb-5 rounded-3xl bg-white p-4 shadow-xl md:p-5">
            <h2 class="text-lg font-black">Nouvelle réinscription</h2>
            <p class="mb-4 text-xs text-slate-500">
                Recherche rapide par matricule, nom ou prénom.
            </p>

            <div class="mb-4">
                <label class="label-pro">Recherche étudiant</label>
                <input
                    v-model="searchEtudiant"
                    type="text"
                    class="input-pro"
                    placeholder="Ex: ST004, Rakoto, Jean..."
                />
            </div>

            <div
                v-if="selectedEtudiant"
                class="mb-4 rounded-2xl bg-teal-50 p-4 text-sm text-teal-900"
            >
                <p><b>Étudiant :</b> {{ selectedEtudiant.nom }} {{ selectedEtudiant.prenoms }}</p>
                <p><b>Matricule :</b> {{ selectedEtudiant.numero || '-' }}</p>
                <p><b>Année actuelle :</b> {{ selectedEtudiant.annee_scolaire || '-' }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <label class="label-pro">Étudiant *</label>
                    <select v-model="form.etudiant_id" class="input-pro">
                        <option value="">Sélectionner un étudiant</option>
                        <option
                            v-for="etudiant in filteredEtudiants"
                            :key="etudiant.id"
                            :value="etudiant.id"
                        >
                            {{ etudiant.numero || '-' }} - {{ etudiant.nom }} {{ etudiant.prenoms }}
                        </option>
                    </select>
                    <p v-if="form.errors.etudiant_id" class="error">
                        {{ form.errors.etudiant_id }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">Année scolaire *</label>
                    <select v-model="form.annee_scolaire" class="input-pro">
                        <option value="">Sélectionner une année</option>
                        <option
                            v-for="annee in anneeScolaires"
                            :key="annee.id"
                            :value="annee.annee || annee.nom"
                        >
                            {{ annee.annee || annee.nom }}
                            {{ annee.active ? ' - ACTIVE' : '' }}
                        </option>
                    </select>
                    <p v-if="form.errors.annee_scolaire" class="error">
                        {{ form.errors.annee_scolaire }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">Classe *</label>
                    <select v-model="form.classe_id" class="input-pro">
                        <option value="">Sélectionner une classe</option>
                        <option
                            v-for="classe in classes"
                            :key="classe.id"
                            :value="classe.id"
                        >
                            {{ classe.nom_classe }} - {{ classe.niveau?.nom_niveau || 'Sans niveau' }}
                        </option>
                    </select>
                    <p v-if="form.errors.classe_id" class="error">
                        {{ form.errors.classe_id }}
                    </p>
                </div>

                <div>
                    <label class="label-pro">Série</label>
                    <select
                        v-model="form.serie_id"
                        class="input-pro"
                        :disabled="!form.classe_id"
                    >
                        <option value="">Aucune série</option>
                        <option
                            v-for="serie in seriesDisponibles"
                            :key="serie.id"
                            :value="serie.id"
                        >
                            Série {{ serie.nom_serie }}
                        </option>
                    </select>
                </div>
            </div>

            <button
                type="button"
                @click="submit"
                :disabled="form.processing"
                class="mt-5 w-full rounded-2xl bg-teal-600 px-6 py-3 text-sm font-black text-white shadow-lg hover:bg-teal-500 disabled:opacity-50 md:w-auto"
            >
                {{ form.processing ? 'Vérification...' : '✅ Réinscrire' }}
            </button>
        </div>

        <div class="rounded-3xl bg-white p-4 shadow-xl md:p-5">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-black">Historique des réinscriptions</h2>
                    <p class="text-xs text-slate-500">
                        Supprimer l’historique efface aussi les données dans la base.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button
                        type="button"
                        @click="showReinscriptions = !showReinscriptions"
                        class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-black text-white"
                    >
                        {{ showReinscriptions ? 'Masquer' : 'Afficher' }}
                    </button>

                    <button
                        type="button"
                        @click="viderAffichage"
                        class="rounded-2xl bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-500"
                    >
                        🗑️ Supprimer historique
                    </button>
                </div>
            </div>

            <div v-if="showReinscriptions">
                <div class="space-y-3 md:hidden">
                    <div
                        v-for="item in localReinscriptions"
                        :key="item.id"
                        class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                    >
                        <p class="font-black">
                            {{ item.etudiant?.numero || '-' }} -
                            {{ item.etudiant?.nom || item.nom }}
                            {{ item.etudiant?.prenoms || item.prenoms }}
                        </p>
                        <p class="text-sm text-slate-600">Année : {{ item.annee_scolaire }}</p>
                        <p class="text-sm text-slate-600">Classe : {{ item.classe?.nom_classe || item.classe || '-' }}</p>
                        <p class="text-sm text-slate-600">Série : {{ item.serie?.nom_serie || item.section || '-' }}</p>
                        <p class="text-sm text-slate-600">Date : {{ item.date_inscription }}</p>
                    </div>

                    <div
                        v-if="localReinscriptions.length === 0"
                        class="rounded-2xl bg-slate-50 p-5 text-center text-sm font-bold text-slate-400"
                    >
                        Historique vide. La prochaine réinscription apparaîtra ici.
                    </div>
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[900px] text-left text-sm">
                        <thead>
                            <tr class="border-b bg-slate-50 text-xs uppercase text-slate-500">
                                <th class="px-4 py-3">Étudiant</th>
                                <th class="px-4 py-3">Année scolaire</th>
                                <th class="px-4 py-3">Classe</th>
                                <th class="px-4 py-3">Série</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Type</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="item in localReinscriptions"
                                :key="item.id"
                                class="border-b hover:bg-teal-50/40"
                            >
                                <td class="px-4 py-3 font-bold">
                                    {{ item.etudiant?.numero || '-' }} -
                                    {{ item.etudiant?.nom || item.nom }}
                                    {{ item.etudiant?.prenoms || item.prenoms }}
                                </td>

                                <td class="px-4 py-3">{{ item.annee_scolaire }}</td>
                                <td class="px-4 py-3">{{ item.classe?.nom_classe || item.classe || '-' }}</td>
                                <td class="px-4 py-3">{{ item.serie?.nom_serie || item.section || '-' }}</td>
                                <td class="px-4 py-3">{{ item.date_inscription }}</td>

                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-teal-100 px-3 py-1 text-xs font-black text-teal-700">
                                        Réinscription
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="localReinscriptions.length === 0">
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    Historique vide. La prochaine réinscription apparaîtra ici.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl bg-slate-50 p-5 text-center text-sm font-bold text-slate-500"
            >
                Historique masqué.
            </div>
        </div>
    </div>
</template>

<style scoped>
.input-pro {
    width: 100%;
    height: 42px;
    border-radius: 14px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 0 12px;
    font-size: 13px;
    outline: none;
}

.input-pro:focus {
    border-color: #14b8a6;
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.12);
}

.input-pro:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.label-pro {
    margin-bottom: 6px;
    display: block;
    font-size: 11px;
    font-weight: 900;
    color: #64748b;
    text-transform: uppercase;
}

.error {
    margin-top: 4px;
    font-size: 12px;
    color: #ef4444;
    font-weight: 700;
}
</style>
