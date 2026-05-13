<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
})

const page = usePage()
const currentUser = computed(() => props.user || page.props.auth?.user || null)

const form = useForm({
    name: currentUser.value?.name || '',
    email: currentUser.value?.email || '',
    photo: null,
})

const userInitial = computed(() => form.name ? form.name.charAt(0).toUpperCase() : 'U')

// Preview dynamique
const previewPhoto = ref(currentUser.value?.photo ? `/storage/${currentUser.value.photo}` : null)

// Progress bar et notification
const uploadProgress = ref(0)
const successMessage = ref('')

function handlePhotoUpload(event) {
    const file = event.target.files[0]
    if (file) {
        form.photo = file
        previewPhoto.value = URL.createObjectURL(file)
    }
}

function submit() {
    form.post(route('profile.custom.update'), {
        preserveScroll: true,
        onProgress: (event) => {
            uploadProgress.value = Math.round((event.loaded / event.total) * 100)
        },
        onSuccess: () => {
            successMessage.value = 'Profil modifié avec succès!'
            setTimeout(() => successMessage.value = '', 3000)
            uploadProgress.value = 0
            // Mettre à jour la preview avec la photo sauvegardée
            if (form.photo) {
                previewPhoto.value = `/storage/${currentUser.value.photo}`
            }
        }
    })
}
</script>

<template>
    <Head title="Mon Profil" />

    <div class="min-h-screen bg-slate-900 text-white py-10 px-6">
        <div class="max-w-3xl mx-auto">

            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black text-cyan-400">
                        Mon Profil
                    </h1>
                    <p class="text-slate-400">
                        Modification du profil utilisateur
                    </p>
                </div>
                <Link href="/dashboard" class="bg-slate-700 hover:bg-slate-600 px-5 py-3 rounded-xl">
                    Retour dashboard
                </Link>
            </div>

            <!-- Card profil -->
            <div class="bg-white/10 rounded-3xl p-8 border border-cyan-500/20 shadow-xl">

                <!-- Photo & Nom -->
                <div class="mb-8 flex items-center gap-4">
                    <div class="relative w-20 h-20 rounded-full bg-cyan-600 flex items-center justify-center text-3xl font-black overflow-hidden">
                        <img v-if="previewPhoto" :src="previewPhoto" class="w-full h-full object-cover" />
                        <span v-else>{{ userInitial }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ form.name || 'Utilisateur' }}</h2>
                        <p class="text-cyan-300">{{ form.email || 'Aucun email' }}</p>
                    </div>
                </div>

                <!-- Formulaire -->
                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Photo -->
                    <div>
                        <label class="block mb-2 text-sm text-slate-300">Photo de profil</label>
                        <input type="file" accept="image/*" @change="handlePhotoUpload" class="text-sm text-white" />
                        <div v-if="uploadProgress > 0" class="w-full bg-slate-700 rounded mt-2 h-2">
                            <div class="bg-green-500 h-2 rounded" :style="{ width: uploadProgress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Nom -->
                    <div>
                        <label class="block mb-2 text-sm text-slate-300">Nom complet</label>
                        <input v-model="form.name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-cyan-400">
                        <p v-if="form.errors.name" class="text-red-400 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block mb-2 text-sm text-slate-300">Email</label>
                        <input v-model="form.email" type="email" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-cyan-400">
                        <p v-if="form.errors.email" class="text-red-400 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>

                    <!-- Button -->
                    <button type="submit" :disabled="form.processing" class="w-full bg-cyan-600 hover:bg-cyan-700 py-3 rounded-xl font-bold disabled:opacity-50">
                        Enregistrer
                    </button>
                </form>

                <!-- Notification succès -->
                <div v-if="successMessage" class="mt-4 text-green-400 font-bold text-center animate-pulse">
                    {{ successMessage }}
                </div>

            </div>
        </div>
    </div>
</template>
