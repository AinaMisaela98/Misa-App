<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import StepHeader from './StepHeader.vue'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  data: { type: Object, default: () => ({}) },
  matricules: { type: Array, default: () => [] },
  matriculeActive: { type: Object, default: null },
  anneeActive: { type: Object, default: null },
})

const photoPreview = ref(props.data?.photo ? `/storage/${props.data.photo}` : null)
const toast = ref('')
const toastColor = ref('red')

const selectedMatriculeId = ref(
  props.data?.matricule_id || props.matriculeActive?.id || ''
)

const selectedMatricule = computed(() => {
  return props.matricules.find(m => Number(m.id) === Number(selectedMatriculeId.value))
})

const placeholderMatricule = computed(() => {
  const prefix = selectedMatricule.value?.prefix || ''
  return prefix ? `${prefix}0001` : 'Choisir un matricule créé'
})

const form = useForm({
  date_inscription: props.data?.date_inscription || new Date().toISOString().slice(0, 10),
  matricule_id: selectedMatriculeId.value,
  numero_matricule: props.data?.numero_matricule || '',
  nom: props.data?.nom || '',
  prenoms: props.data?.prenoms || '',
  genre: props.data?.genre || '',
  date_naissance: props.data?.date_naissance || '',
  lieu_naissance: props.data?.lieu_naissance || '',
  signe_particulier: props.data?.signe_particulier || '',
  allergique: props.data?.allergique || '',
  ecole_origine: props.data?.ecole_origine || '',
  telephone: props.data?.telephone || '',
  adresse: props.data?.adresse || '',
  email: props.data?.email || '',
  code_postal: props.data?.code_postal || '',
  ville: props.data?.ville || '',
  taille: props.data?.taille || '',
  poids: props.data?.poids || '',
  photo: null,
})

watch(selectedMatriculeId, value => {
  form.matricule_id = value
})

function showToast(message, color = 'red') {
  toast.value = message
  toastColor.value = color

  setTimeout(() => {
    toast.value = ''
  }, 4000)
}

function handlePhoto(e) {
  const file = e.target.files[0]
  form.photo = file

  if (file) {
    photoPreview.value = URL.createObjectURL(file)
  }
}

function submit() {
  form.clearErrors()

  form.post(route('inscriptions.etudiant.store'), {
    forceFormData: true,
    preserveScroll: true,

    onError: (errors) => {
      if (errors.numero_matricule) {
        showToast('❌ Matricule déjà enregistré.', 'red')
        return
      }

      if (errors.matricule_id) {
        showToast('❌ Veuillez choisir un modèle de matricule.', 'red')
        return
      }

      showToast('❌ Veuillez vérifier les informations saisies.', 'red')
    },
  })
}
</script>

<template>
  <Head title="Inscription étudiant" />

  <div class="min-h-screen bg-slate-100 p-4">
    <div
      v-if="toast"
      :class="[
        'fixed right-4 top-4 z-[9999] max-w-[92vw] rounded-2xl px-5 py-3 text-sm font-black text-white shadow-2xl',
        toastColor === 'green' ? 'bg-emerald-600' : 'bg-red-600'
      ]"
    >
      {{ toast }}
    </div>

    <div class="mx-auto max-w-7xl overflow-hidden rounded-2xl bg-white shadow-xl">
      <div class="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
        <div>
          <h1 class="text-xl font-bold">Fiche d'inscription</h1>
          <p class="text-sm text-slate-300">Étape 1 : information sur l'étudiant</p>
        </div>

        <div class="flex gap-2">
          <Link :href="route('inscriptions.reset')" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-bold">
            Réinitialiser
          </Link>
        </div>
      </div>

      <div class="p-8">
        <StepHeader :step="1" />

        <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <div class="rounded-xl border border-slate-200 p-6">
            <h2 class="mb-6 text-lg font-bold text-slate-800">Info sur l'étudiant</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="label">Date inscription</label>
                <input v-model="form.date_inscription" type="date" class="input" />
              </div>

              <div>
                <label class="label">Matricule créé</label>
                <select v-model="selectedMatriculeId" class="input">
                  <option value="">Choisir un matricule</option>
                  <option v-for="m in matricules" :key="m.id" :value="m.id">
                    {{ m.prefix }} {{ m.active ? '- ACTIVE' : '' }}
                  </option>
                </select>
                <p v-if="form.errors.matricule_id" class="error">{{ form.errors.matricule_id }}</p>
              </div>

              <div>
                <label class="label">Numéro matricule manuel *</label>
                <input
                  v-model="form.numero_matricule"
                  type="text"
                  :placeholder="placeholderMatricule"
                  class="input font-black text-slate-700"
                />
                <p v-if="form.errors.numero_matricule" class="error">
                  {{ form.errors.numero_matricule }}
                </p>
              </div>

              <div>
                <label class="label">Année scolaire active</label>
                <input
                  :value="props.anneeActive?.annee || props.anneeActive?.nom || ''"
                  readonly
                  class="input bg-slate-100 font-black text-blue-700"
                />
              </div>

              <div>
                <label class="label">Nom *</label>
                <input v-model="form.nom" placeholder="Nom" class="input" />
                <p v-if="form.errors.nom" class="error">{{ form.errors.nom }}</p>
              </div>

              <div>
                <label class="label">Prénoms</label>
                <input v-model="form.prenoms" placeholder="Prénoms" class="input" />
              </div>

              <div>
                <label class="label">Genre</label>
                <select v-model="form.genre" class="input">
                  <option value="">Choisir</option>
                  <option value="Masculin">Masculin</option>
                  <option value="Féminin">Féminin</option>
                </select>
              </div>

              <div>
                <label class="label">Date de naissance</label>
                <input v-model="form.date_naissance" type="date" class="input" />
              </div>

              <div>
                <label class="label">Lieu de naissance</label>
                <input v-model="form.lieu_naissance" placeholder="Lieu de naissance" class="input" />
              </div>

              <div>
                <label class="label">Téléphone</label>
                <input v-model="form.telephone" placeholder="Téléphone" class="input" />
              </div>

              <div class="md:col-span-2">
                <label class="label">Adresse *</label>
                <input v-model="form.adresse" placeholder="Adresse" class="input" />
                <p v-if="form.errors.adresse" class="error">{{ form.errors.adresse }}</p>
              </div>

              <div>
                <label class="label">Email</label>
                <input v-model="form.email" placeholder="Adresse e-mail" class="input" />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
              </div>

              <div>
                <label class="label">Ville</label>
                <input v-model="form.ville" placeholder="Ville" class="input" />
              </div>

              <div>
                <label class="label">Code postal</label>
                <input v-model="form.code_postal" placeholder="Code postal" class="input" />
              </div>

              <div>
                <label class="label">École d'origine</label>
                <input v-model="form.ecole_origine" placeholder="École d'origine" class="input" />
              </div>

              <div>
                <label class="label">Taille</label>
                <input v-model="form.taille" placeholder="Taille" class="input" />
              </div>

              <div>
                <label class="label">Poids</label>
                <input v-model="form.poids" placeholder="Poids" class="input" />
              </div>

              <div>
                <label class="label">Signe particulier</label>
                <input v-model="form.signe_particulier" placeholder="Signe particulier" class="input" />
              </div>

              <div>
                <label class="label">Allergique</label>
                <input v-model="form.allergique" placeholder="Allergique" class="input" />
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-slate-200 p-6">
            <h2 class="mb-6 text-lg font-bold text-slate-800">Photo de l'étudiant</h2>

            <div class="flex h-[420px] items-center justify-center overflow-hidden rounded-xl border bg-slate-50">
              <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />

              <div v-else class="text-center text-slate-400">
                <div class="text-8xl">👤</div>
                <p class="mt-3 font-semibold">Aucune photo sélectionnée</p>
              </div>
            </div>

            <input type="file" accept="image/*" @change="handlePhoto" class="mt-4 w-full rounded-lg border p-2" />
            <p v-if="form.errors.photo" class="error">{{ form.errors.photo }}</p>
          </div>

          <div class="flex justify-end lg:col-span-2">
            <button type="submit" class="btn-primary" :disabled="form.processing">
              Étape suivante →
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input {
  @apply w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100;
}
.label {
  @apply mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500;
}
.error {
  @apply mt-1 text-sm font-semibold text-red-500;
}
.btn-primary {
  @apply rounded-xl bg-slate-900 px-7 py-3 font-bold text-white transition hover:bg-slate-800 disabled:opacity-50;
}
</style>
