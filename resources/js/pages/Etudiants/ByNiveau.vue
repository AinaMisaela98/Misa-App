<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    etudiants: Array,
    niveau: String
});

// 🔍 SEARCH
const search = ref('');

// 🔥 INIT SHOW
onMounted(() => {
    props.etudiants.forEach(e => {
        e.show = false;
    });
});

// ================== FILTER ==================
const filtered = computed(() => {
    if (!search.value) return props.etudiants;

    return props.etudiants.filter(e =>
        e.nom.toLowerCase().includes(search.value.toLowerCase()) ||
        e.email.toLowerCase().includes(search.value.toLowerCase())
    );
});

// ================== COLOR BY LEVEL ==================
function badgeColor(niveau) {
    if (niveau === 'Débutant') return 'bg-green-500';
    if (niveau === 'Intermédiaire') return 'bg-yellow-500';
    if (niveau === 'Avancé') return 'bg-red-500';
    return 'bg-gray-500';
}
</script>

<template>
<Head :title="niveau" />

<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-800 text-white p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold text-yellow-400">
            🎓 Niveau: {{ niveau }}
        </h1>
        <!-- EXPORTATION EXCEL-->
      <a
             :href="`/etudiants/export/${niveau}`"
             class="bg-green-500 px-4 py-2 rounded text-black hover:bg-green-600"
>
    📥 Export Excel
      </a>
        <!-- BACK BUTTON -->
        <a href="/dashboard"
           class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-500">
            ⬅ Retour Dashboard
        </a>

    </div>

    <!-- COUNTER -->
    <div class="mb-4 text-sm text-gray-300">
        👥 Total étudiants: <span class="font-bold text-white">{{ filtered.length }}</span>
    </div>

    <!-- SEARCH -->
    <input
        v-model="search"
        type="text"
        placeholder="🔍 Rechercher..."
        class="w-full mb-6 p-3 rounded bg-gray-800 border border-gray-700"
    />

    <!-- LIST -->
    <div class="bg-gray-800 rounded-2xl p-6 shadow-lg">

        <table class="w-full text-left">

            <thead>
                <tr class="text-gray-400 border-b border-gray-700">
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <template v-for="e in filtered" :key="e.id">

                    <!-- ROW -->
                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition">

                        <td class="py-3 font-semibold flex items-center gap-2">

                            <!-- COLOR BADGE -->
                            <span
                                class="w-3 h-3 rounded-full"
                                :class="badgeColor(e.niveau)">
                            </span>

                            {{ e.nom }}

                        </td>

                        <td>
                            <button
                                @click="e.show = !e.show"
                                class="bg-blue-500 px-3 py-1 rounded text-white text-xs"
                            >
                                ℹ️ Info
                            </button>
                        </td>

                    </tr>

                    <!-- DETAILS + ANIMATION -->
                    <tr v-if="e.show"
                        class="transition-all duration-300 ease-in-out">

                        <td colspan="2"
                            class="bg-gray-900 p-4 text-sm text-gray-300 animate-fade">

                            📧 Email: {{ e.email }} <br>
                            📞 Téléphone: {{ e.telephone }} <br>
                            🏠 Adresse: {{ e.adresse }} <br>
                            🎓 Niveau:
                            <span class="font-bold text-yellow-400">
                                {{ e.niveau }}
                            </span>

                        </td>

                    </tr>

                </template>

            </tbody>

        </table>

    </div>

</div>
</template>

<style>
/* 🔥 SIMPLE ANIMATION SLIDE */
.animate-fade {
    animation: fadeSlide 0.25s ease-in-out;
}

@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>