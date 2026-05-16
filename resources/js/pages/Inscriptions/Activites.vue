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
  activites: props.data?.activites || [],
  observation: props.data?.observation || '',
})

function submit() {
  form.post(route('inscriptions.activites.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Activités" />

  <div class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto max-w-5xl rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-2xl font-bold text-slate-900">Activités</h1>
      <p class="mb-8 text-slate-500">Étape 5 : activités et observation</p>

      <StepHeader :step="5" />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <label class="activity">
            <input v-model="form.activites" value="Formation" type="checkbox" />
            Formation
          </label>

          <label class="activity">
            <input v-model="form.activites" value="Stage" type="checkbox" />
            Stage
          </label>

          <label class="activity">
            <input v-model="form.activites" value="Atelier pratique" type="checkbox" />
            Atelier pratique
          </label>

          <label class="activity">
            <input v-model="form.activites" value="Évaluation" type="checkbox" />
            Évaluation
          </label>

          <label class="activity">
            <input v-model="form.activites" value="Certification" type="checkbox" />
            Certification
          </label>

          <label class="activity">
            <input v-model="form.activites" value="Suivi" type="checkbox" />
            Suivi
          </label>
        </div>

        <div class="mt-6">
          <label class="label">Observation</label>
          <textarea
            v-model="form.observation"
            class="input min-h-[140px]"
            placeholder="Observation..."
          ></textarea>
        </div>

        <div class="mt-8 flex justify-between">
          <Link :href="route('inscriptions.niveau')" class="btn-secondary">← Retour</Link>

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
.activity {
  @apply flex items-center gap-3 rounded-xl border border-slate-300 bg-slate-50 p-4 font-bold text-slate-700;
}
.btn-primary {
  @apply rounded-xl bg-slate-900 px-7 py-3 font-bold text-white hover:bg-slate-800 disabled:opacity-50;
}
.btn-secondary {
  @apply rounded-xl bg-slate-200 px-7 py-3 font-bold text-slate-700 hover:bg-slate-300;
}
</style>
