<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import StepHeader from './StepHeader.vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({}),
  },
})

const form = useForm({
  tuteur_nom: props.data?.tuteur_nom || '',
  tuteur_telephone: props.data?.tuteur_telephone || '',
  tuteur_lien: props.data?.tuteur_lien || '',
  tuteur_adresse: props.data?.tuteur_adresse || '',
})

function submit() {
  form.post(route('inscriptions.tuteurs.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Tuteur" />

  <div class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto max-w-5xl rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-2xl font-bold text-slate-900">Informations du tuteur</h1>
      <p class="mb-8 text-slate-500">Étape 3 : responsable ou tuteur</p>

      <StepHeader :step="3" />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="label">Nom du tuteur</label>
            <input v-model="form.tuteur_nom" class="input" placeholder="Nom du tuteur" />
          </div>

          <div>
            <label class="label">Téléphone</label>
            <input v-model="form.tuteur_telephone" class="input" placeholder="Téléphone du tuteur" />
          </div>

          <div>
            <label class="label">Lien avec l'étudiant</label>
            <input v-model="form.tuteur_lien" class="input" placeholder="Ex : Oncle, Tante, Responsable..." />
          </div>

          <div>
            <label class="label">Adresse</label>
            <input v-model="form.tuteur_adresse" class="input" placeholder="Adresse du tuteur" />
          </div>
        </div>

        <div class="mt-8 flex justify-between">
          <Link :href="route('inscriptions.parents')" class="btn-secondary">← Retour</Link>

          <button type="submit" class="btn-primary" :disabled="form.processing">
            Étape suivante →
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.input {
  @apply w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100;
}
.label {
  @apply mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500;
}
.btn-primary {
  @apply rounded-xl bg-slate-900 px-7 py-3 font-bold text-white hover:bg-slate-800 disabled:opacity-50;
}
.btn-secondary {
  @apply rounded-xl bg-slate-200 px-7 py-3 font-bold text-slate-700 hover:bg-slate-300;
}
</style>
