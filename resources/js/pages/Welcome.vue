<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-[#070707] text-white overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-gradient-to-br from-black via-[#120000] to-black"></div>
      <div class="absolute top-20 left-10 w-72 h-72 bg-red-600/30 rounded-full blur-3xl"></div>
      <div class="absolute bottom-10 right-10 w-96 h-96 bg-red-800/30 rounded-full blur-3xl"></div>
    </div>

    <!-- Navbar -->
    <nav class="relative z-10 flex items-center justify-between px-8 py-6">
      <div class="text-2xl font-black tracking-wide">
        <span class="text-red-600">Music</span> Misa
      </div>

      <div class="flex gap-4">
        <Link
          :href="route('login')"
          class="px-5 py-2 rounded-full border border-red-600 text-red-500 hover:bg-red-600 hover:text-white transition"
        >
          Login
        </Link>

        <Link
          :href="route('register')"
          class="px-5 py-2 rounded-full bg-red-600 hover:bg-red-700 transition font-semibold"
        >
          Register
        </Link>
      </div>
    </nav>

    <!-- Hero -->
    <main class="relative z-10 min-h-[80vh] flex items-center justify-center px-6">
      <div class="max-w-5xl text-center">
        <p class="text-red-500 font-semibold uppercase tracking-[0.4em] mb-5">
          Welcome to the sound
        </p>

        <h1 class="text-5xl md:text-7xl font-black leading-tight">
          Découvrez une nouvelle expérience
          <span class="text-red-600"> musicale</span>
        </h1>

        <p class="mt-6 text-gray-300 text-lg md:text-xl max-w-2xl mx-auto">
          Connectez-vous pour accéder à votre espace admin, gérer vos contenus,
          artistes, musiques et utilisateurs avec une interface moderne.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-5">
          <Link
            :href="route('login')"
            class="px-8 py-4 rounded-full bg-red-600 hover:bg-red-700 transition font-bold shadow-lg shadow-red-600/30"
          >
            Se connecter
          </Link>

          <Link
            :href="route('register')"
            class="px-8 py-4 rounded-full bg-white text-black hover:bg-gray-200 transition font-bold"
          >
            Créer un compte
          </Link>
        </div>
      </div>
    </main>
  </div>
</template>
