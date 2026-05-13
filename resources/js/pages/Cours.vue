<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// STATS
const stats = ref({
    total: 0,
    debutant: 0,
    intermediaire: 0,
    avance: 0,
})

// LOAD DATA
onMounted(async () => {

    try {

        const res = await axios.get('/stats-etudiants')

        console.log(res.data)

        stats.value = res.data

    } catch (error) {

        console.error('Erreur API:', error)

    }

})
</script>

<template>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white p-6">

        <!-- HEADER -->
        <div class="mb-8">

            <h1 class="text-4xl font-extrabold tracking-wide">
                🎵 Music School Dashboard By Misaela
            </h1>

            <p class="text-gray-400 mt-2">
                Statistiques des étudiants par niveau
            </p>

        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- TOTAL -->
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 shadow-lg">

                <div class="text-gray-300 text-sm">
                    Total Étudiants
                </div>

                <div class="text-3xl font-bold mt-2">
                    {{ stats.total }}
                </div>

                <div class="mt-2 text-blue-400 text-sm">
                    📊 Global
                </div>

            </div>

            <!-- DEBUTANT -->
            <div class="bg-green-500/20 border border-green-400/30 rounded-2xl p-6 shadow-lg">

                <div class="text-green-300 text-sm">
                    Débutant
                </div>

                <div class="text-3xl font-bold mt-2">
                    {{ stats.debutant }}
                </div>

                <div class="mt-2 text-green-400 text-sm">
                    🎸 Niveau 1
                </div>

            </div>

            <!-- INTERMEDIAIRE -->
            <div class="bg-yellow-500/20 border border-yellow-400/30 rounded-2xl p-6 shadow-lg">

                <div class="text-yellow-300 text-sm">
                    Intermédiaire
                </div>

                <div class="text-3xl font-bold mt-2">
                    {{ stats.intermediaire }}
                </div>

                <div class="mt-2 text-yellow-400 text-sm">
                    🎼 Niveau 2
                </div>

            </div>

            <!-- AVANCE -->
            <div class="bg-red-500/20 border border-red-400/30 rounded-2xl p-6 shadow-lg">

                <div class="text-red-300 text-sm">
                    Avancé
                </div>

                <div class="text-3xl font-bold mt-2">
                    {{ stats.avance }}
                </div>

                <div class="mt-2 text-red-400 text-sm">
                    🔥 Pro Level
                </div>

            </div>

        </div>

    </div>

</template>