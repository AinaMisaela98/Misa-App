<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    matricules: { type: Array, default: () => [] },
    active: { type: Object, default: null },
})

const toast = ref('')
const toastColor = ref('green')

const form = useForm({
    prefix: '',
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

    if (!form.prefix) {
        form.setError('prefix', 'Le préfixe est obligatoire')
        showToast('Veuillez entrer un préfixe matricule', 'red')
        return
    }

    form.post(route('parametres.matricule.store'), {
        preserveScroll: true,

        onSuccess: () => {
            form.reset()
            showToast('Matricule enregistré avec succès', 'green')

            router.reload({
                only: ['matricules', 'active'],
                preserveScroll: true,
            })
        },

        onError: () => {
            showToast('Erreur lors de l’enregistrement', 'red')
        },
    })
}

function destroy(id) {
    if (!confirm('Voulez-vous vraiment supprimer ce matricule ?')) return

    router.post(`/parametres/matricule/${id}`, {
        _method: 'DELETE',
    }, {
        preserveScroll: true,

        onSuccess: () => {
            showToast('Matricule supprimé avec succès', 'green')

            router.reload({
                only: ['matricules', 'active'],
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
    <Head title="Paramètre matricule" />

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
                    Paramètre matricule
                </h1>

                <p class="text-sm text-slate-500">
                    Création du préfixe matricule automatique
                </p>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-3 md:flex-row">
                <div class="flex-1">
                    <input
                        v-model="form.prefix"
                        class="input"
                        placeholder="Ex: ST"
                    />

                    <p v-if="form.errors.prefix" class="mt-2 text-xs font-bold text-red-600">
                        {{ form.errors.prefix }}
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
                    Liste des préfixes matricules créés
                </h2>

                <div
                    v-if="props.matricules.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-400"
                >
                    Aucun matricule enregistré.
                </div>

                <div
                    v-for="m in props.matricules"
                    :key="m.id"
                    class="mb-2 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4"
                >
                    <div>
                        <div class="font-black text-slate-900">
                            {{ m.prefix }}
                        </div>

                        <div class="text-xs text-slate-500">
                            Préfixe matricule créé
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div
                            v-if="m.active"
                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-black text-green-700"
                        >
                            ACTIVE
                        </div>

                        <button
                            type="button"
                            @click="destroy(m.id)"
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
