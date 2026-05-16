<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    annees: { type: Array, default: () => [] },
    active: { type: Object, default: null },
})

const toast = ref('')
const toastColor = ref('green')

const form = useForm({
    annee: '',
})

function showToast(message, color = 'green') {
    toast.value = message
    toastColor.value = color

    setTimeout(() => {
        toast.value = ''
    }, 3000)
}

function submit() {
    form.clearErrors()

    if (!form.annee) {
        form.setError('annee', 'L’année scolaire est obligatoire')
        showToast('Veuillez entrer une année scolaire', 'red')
        return
    }

    form.post(route('parametres.annee.store'), {
        preserveScroll: true,

        onSuccess: () => {
            form.reset()
            showToast('Année scolaire enregistrée avec succès', 'green')

            router.reload({
                only: ['annees', 'active'],
                preserveScroll: true,
            })
        },

        onError: () => {
            showToast('Erreur lors de l’enregistrement', 'red')
        },
    })
}

function activate(id) {
    router.post(route('parametres.annee.activate', id), {}, {
        preserveScroll: true,

        onSuccess: () => {
            showToast('Année scolaire activée avec succès', 'green')

            router.reload({
                only: ['annees', 'active'],
                preserveScroll: true,
            })
        },

        onError: () => {
            showToast('Activation impossible', 'red')
        },
    })
}

function destroy(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette année scolaire ?')) return

    router.post(`/parametres/annee/${id}`, {
        _method: 'DELETE',
    }, {
        preserveScroll: true,

        onSuccess: () => {
            showToast('Année scolaire supprimée avec succès', 'green')

            router.reload({
                only: ['annees', 'active'],
                preserveScroll: true,
            })
        },

        onError: () => {
            showToast('Suppression impossible', 'red')
        },
    })
}
</script>

<template>
    <Head title="Année scolaire" />

    <div class="min-h-screen bg-slate-100 p-6">
        <div
            v-if="toast"
            :class="[
                'fixed right-5 top-5 z-50 rounded-2xl px-5 py-3 text-sm font-black text-white shadow-xl',
                toastColor === 'green' ? 'bg-green-600' : 'bg-red-600'
            ]"
        >
            {{ toast }}
        </div>

        <div class="mx-auto max-w-3xl rounded-3xl bg-white p-6 shadow-xl">
            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900">
                    Année scolaire
                </h1>

                <p class="text-sm text-slate-500">
                    Gestion de l’année scolaire active
                </p>
            </div>

            <form
                @submit.prevent="submit"
                class="flex flex-col gap-3 md:flex-row"
            >
                <div class="flex-1">
                    <input
                        v-model="form.annee"
                        class="input"
                        placeholder="Ex: 2026-2027"
                    />

                    <p
                        v-if="form.errors.annee"
                        class="mt-2 text-xs font-bold text-red-600"
                    >
                        {{ form.errors.annee }}
                    </p>
                </div>

                <button
                    type="submit"
                    class="btn"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Enregistrement...' : 'Ajouter' }}
                </button>
            </form>

            <div class="mt-8">
                <h2 class="mb-3 text-sm font-black uppercase text-slate-500">
                    Liste des années scolaires créées
                </h2>

                <div
                    v-if="props.annees.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-400"
                >
                    Aucune année scolaire enregistrée.
                </div>

                <div
                    v-for="a in props.annees"
                    :key="a.id"
                    class="mb-2 flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <div class="font-black text-slate-900">
                            {{ a.annee }}
                        </div>

                        <div class="text-xs text-slate-500">
                            Année scolaire créée
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div
                            v-if="a.active"
                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-black text-green-700"
                        >
                            ACTIVE
                        </div>

                        <button
                            v-else
                            type="button"
                            @click="activate(a.id)"
                            class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-black text-white hover:bg-blue-700"
                        >
                            Activer
                        </button>

                        <button
                            type="button"
                            @click="destroy(a.id)"
                            class="rounded-xl bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-700"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.input {
    @apply w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-slate-900;
}

.btn {
    @apply rounded-xl bg-slate-900 px-6 py-3 font-black text-white disabled:opacity-50;
}
</style>
