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
  pere_nom: props.data?.pere_nom || '',
  pere_telephone: props.data?.pere_telephone || '',
  pere_profession: props.data?.pere_profession || '',
  mere_nom: props.data?.mere_nom || '',
  mere_telephone: props.data?.mere_telephone || '',
  mere_profession: props.data?.mere_profession || '',
})

function submit() {
  form.post(route('inscriptions.parents.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Informations parents" />

  <div class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto max-w-6xl rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-2xl font-bold text-slate-900">Informations des parents</h1>
      <p class="mb-8 text-slate-500">Étape 2 : père et mère</p>

      <StepHeader :step="2" />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 p-6">
            <h2 class="mb-5 text-lg font-bold">Père</h2>

            <label class="label">Nom du père</label>
            <input v-model="form.pere_nom" class="input" placeholder="Nom du père" />

            <label class="label mt-4">Téléphone</label>
            <input v-model="form.pere_telephone" class="input" placeholder="Téléphone du père" />

            <label class="label mt-4">Profession</label>
            <input v-model="form.pere_profession" class="input" placeholder="Profession du père" />
          </div>

          <div class="rounded-2xl border border-slate-200 p-6">
            <h2 class="mb-5 text-lg font-bold">Mère</h2>

            <label class="label">Nom de la mère</label>
            <input v-model="form.mere_nom" class="input" placeholder="Nom de la mère" />

            <label class="label mt-4">Téléphone</label>
            <input v-model="form.mere_telephone" class="input" placeholder="Téléphone de la mère" />

            <label class="label mt-4">Profession</label>
            <input v-model="form.mere_profession" class="input" placeholder="Profession de la mère" />
          </div>
        </div>

        <div class="mt-8 flex justify-between">
          <Link :href="route('inscriptions.create')" class="btn-secondary">← Retour</Link>

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
