<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import StepHeader from './StepHeader.vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({}),
  },
})

const form = useForm({})

function submit() {
  form.post(route('inscriptions.finaliser'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Validation inscription" />

  <div class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto max-w-6xl rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-2xl font-bold text-slate-900">Validation finale</h1>
      <p class="mb-8 text-slate-500">Étape 6 : vérification avant enregistrement</p>

      <StepHeader :step="6" />

      <div class="mb-6 rounded-2xl bg-green-50 p-6 text-green-800">
        <h2 class="text-xl font-bold">C'est prêt ✅</h2>
        <p>Vérifiez les informations puis validez définitivement l'inscription.</p>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border p-5">
          <h3 class="mb-4 font-bold text-slate-900">Étudiant</h3>
          <p><b>Nom :</b> {{ data.nom }}</p>
          <p><b>Prénoms :</b> {{ data.prenoms }}</p>
          <p><b>Genre :</b> {{ data.genre }}</p>
          <p><b>Date naissance :</b> {{ data.date_naissance }}</p>
          <p><b>Téléphone :</b> {{ data.telephone }}</p>
          <p><b>Adresse :</b> {{ data.adresse }}</p>
          <p><b>Email :</b> {{ data.email }}</p>
        </div>

        <div class="rounded-2xl border p-5">
          <h3 class="mb-4 font-bold text-slate-900">Parents</h3>
          <p><b>Père :</b> {{ data.pere_nom }}</p>
          <p><b>Tél père :</b> {{ data.pere_telephone }}</p>
          <p><b>Profession père :</b> {{ data.pere_profession }}</p>
          <hr class="my-3" />
          <p><b>Mère :</b> {{ data.mere_nom }}</p>
          <p><b>Tél mère :</b> {{ data.mere_telephone }}</p>
          <p><b>Profession mère :</b> {{ data.mere_profession }}</p>
        </div>

        <div class="rounded-2xl border p-5">
          <h3 class="mb-4 font-bold text-slate-900">Tuteur</h3>
          <p><b>Nom :</b> {{ data.tuteur_nom }}</p>
          <p><b>Téléphone :</b> {{ data.tuteur_telephone }}</p>
          <p><b>Lien :</b> {{ data.tuteur_lien }}</p>
          <p><b>Adresse :</b> {{ data.tuteur_adresse }}</p>
        </div>

        <div class="rounded-2xl border p-5">
          <h3 class="mb-4 font-bold text-slate-900">Niveau & frais</h3>
          <p><b>Niveau :</b> {{ data.niveau }}</p>
          <p><b>Formation :</b> {{ data.formation }}</p>
          <p><b>Frais inscription :</b> {{ data.frais_inscription }}</p>
          <p><b>Frais formation :</b> {{ data.frais_formation }}</p>
          <p><b>Mode paiement :</b> {{ data.mode_paiement }}</p>
        </div>

        <div class="rounded-2xl border p-5 lg:col-span-2">
          <h3 class="mb-4 font-bold text-slate-900">Activités</h3>
          <p><b>Activités :</b> {{ data.activites?.join(', ') }}</p>
          <p class="mt-3"><b>Observation :</b> {{ data.observation }}</p>
        </div>
      </div>

      <div class="mt-8 flex justify-between">
        <Link :href="route('inscriptions.activites')" class="btn-secondary">← Retour</Link>

        <button @click="submit" class="btn-primary" :disabled="form.processing">
          Valider l'inscription
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
p {
  @apply mb-2 text-sm text-slate-700;
}
.btn-primary {
  @apply rounded-xl bg-green-600 px-7 py-3 font-bold text-white hover:bg-green-700 disabled:opacity-50;
}
.btn-secondary {
  @apply rounded-xl bg-slate-200 px-7 py-3 font-bold text-slate-700 hover:bg-slate-300;
}
</style>
