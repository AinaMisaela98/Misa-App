<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import StepHeader from './StepHeader.vue'

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },

    classes: {
        type: Array,
        default: () => [],
    },

    series: {
        type: Array,
        default: () => [],
    },

    anneeActive: {
        type: String,
        default: '',
    },
})

const form = useForm({
  classe_id: props.data?.classe_id ? String(props.data.classe_id) : '',
  serie_id: props.data?.serie_id ? String(props.data.serie_id) : '',
  formation: props.data?.formation || '',
  frais_inscription: props.data?.frais_inscription || '',
  frais_formation: props.data?.frais_formation || '',
  mode_paiement: props.data?.mode_paiement || '',
})

function classeName(classe) {
  return classe.nom || classe.name || classe.libelle || classe.nom_classe || classe.titre || 'Classe sans nom'
}

function serieName(serie) {
  return serie.nom || serie.name || serie.libelle || serie.nom_serie || serie.titre || 'Série sans nom'
}

const filteredSeries = computed(() => {
  if (!form.classe_id) return []

  return props.series.filter((serie) => {
    return (
      String(serie.classe_id || '') === String(form.classe_id) ||
      String(serie.classe?.id || '') === String(form.classe_id)
    )
  })
})

watch(() => form.classe_id, () => {
  form.serie_id = ''
})

function submit() {
  form.post(route('inscriptions.niveau.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Niveau et frais" />
  <div class="mb-4 rounded-2xl bg-blue-50 px-4 py-3 text-sm font-black text-blue-700">
    📅 Année scolaire active : {{ anneeActive || 'Non définie' }}
</div>

  <div class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto max-w-5xl rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-2xl font-bold text-slate-900">
        Classe & Série
      </h1>

      <p class="mb-8 text-slate-500">
        Étape 4 : choix classe, série et paiement
      </p>

      <StepHeader :step="4" />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

          <div>
            <label class="label">Classe</label>

            <select v-model="form.classe_id" class="input">
              <option value="">Choisir la classe</option>

              <option
                v-for="classe in classes"
                :key="classe.id"
                :value="String(classe.id)"
              >
                {{ classeName(classe) }}
              </option>
            </select>
          </div>

          <div>
            <label class="label">Série</label>

            <select
              v-model="form.serie_id"
              class="input"
              :disabled="!form.classe_id"
            >
              <option value="">
                {{ form.classe_id ? 'Choisir la série' : 'Choisir d’abord une classe' }}
              </option>

              <option
                v-for="serie in filteredSeries"
                :key="serie.id"
                :value="String(serie.id)"
              >
                {{ serieName(serie) }}
              </option>
            </select>
          </div>

          <div>
            <label class="label">Formation</label>
            <input
              v-model="form.formation"
              class="input"
              placeholder="Nom de la formation"
            />
          </div>

          <div>
            <label class="label">Frais inscription</label>
            <input
              v-model="form.frais_inscription"
              type="number"
              class="input"
              placeholder="0"
            />
          </div>

          <div>
            <label class="label">Frais formation</label>
            <input
              v-model="form.frais_formation"
              type="number"
              class="input"
              placeholder="0"
            />
          </div>

          <div class="md:col-span-2">
            <label class="label">Mode de paiement</label>

            <select v-model="form.mode_paiement" class="input">
              <option value="">Choisir le mode de paiement</option>
              <option value="Espèce">Espèce</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Virement">Virement</option>
              <option value="Chèque">Chèque</option>
            </select>
          </div>
        </div>

        <div class="mt-8 flex justify-between">
          <Link :href="route('inscriptions.tuteurs')" class="btn-secondary">
            ← Retour
          </Link>

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
  width: 100%;
  border-radius: 0.75rem;
  border: 1px solid #cbd5e1;
  background-color: #ffffff !important;
  color: #0f172a !important;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  outline: none;
}

.input:focus {
  border-color: #06b6d4;
  box-shadow: 0 0 0 4px #cffafe;
}

.input:disabled {
  background-color: #f1f5f9 !important;
  color: #475569 !important;
}

.input option {
  color: #0f172a !important;
  background-color: #ffffff !important;
}

.label {
  @apply mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500;
}

.btn-primary {
  @apply rounded-xl bg-slate-900 px-7 py-3 font-bold text-white transition hover:bg-slate-800 disabled:opacity-50;
}

.btn-secondary {
  @apply rounded-xl bg-slate-200 px-7 py-3 font-bold text-slate-700 transition hover:bg-slate-300;
}
</style>
