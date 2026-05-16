<script setup>
defineProps({
  step: {
    type: Number,
    default: 1,
  },
})
</script>

<template>
  <div class="mb-10">
    <div class="relative">
      <div class="absolute left-0 top-6 h-1 w-full rounded bg-slate-200"></div>

      <div
        class="absolute left-0 top-6 h-1 rounded bg-cyan-500 transition-all"
        :style="{ width: ((step - 1) / 5) * 100 + '%' }"
      ></div>

      <div class="relative grid grid-cols-6 gap-2">
        <div
          v-for="item in [
            'Étudiant',
            'Parents',
            'Tuteur',
            'Niveau',
            'Activités',
            'Validation'
          ]"
          :key="item"
          class="text-center"
        >
          <div
            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border-4 text-sm font-bold shadow-sm"
            :class="step >= ([
              'Étudiant',
              'Parents',
              'Tuteur',
              'Niveau',
              'Activités',
              'Validation'
            ].indexOf(item) + 1)
              ? 'border-cyan-500 bg-slate-900 text-white'
              : 'border-slate-200 bg-white text-slate-400'"
          >
            {{ ['🎓','👨‍👩‍👧','👤','💳','▶','🏁'][[
              'Étudiant',
              'Parents',
              'Tuteur',
              'Niveau',
              'Activités',
              'Validation'
            ].indexOf(item)] }}
          </div>

          <p
            class="mt-2 text-xs font-semibold"
            :class="step >= ([
              'Étudiant',
              'Parents',
              'Tuteur',
              'Niveau',
              'Activités',
              'Validation'
            ].indexOf(item) + 1)
              ? 'text-slate-900'
              : 'text-slate-400'"
          >
            {{ item }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
