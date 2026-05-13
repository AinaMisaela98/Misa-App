<script setup>
import { ref } from 'vue'
import ChatList from '../Components/ChatList.vue'
import ChatBox from '../Components/ChatBox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({
    layout: AuthenticatedLayout
})

const search = ref('')
</script>

<template>
    <div class="relative h-screen overflow-hidden bg-black text-white">

        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-gray-950 to-black"></div>

        <div class="absolute top-[-120px] left-[-120px] w-[500px] h-[500px] bg-cyan-500/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-120px] right-[-120px] w-[500px] h-[500px] bg-purple-500/20 blur-[120px] rounded-full"></div>

        <div class="relative z-10 h-full p-3 md:p-6">
            <div class="h-full flex rounded-[28px] overflow-hidden border border-white/10 bg-white/5 backdrop-blur-2xl shadow-[0_0_80px_rgba(0,0,0,0.6)]">

                <aside class="w-[360px] flex flex-col border-r border-white/10 bg-black/20">

                    <div class="p-5 flex items-center justify-between border-b border-white/10">
                        <div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                                Messages
                            </h1>
                            <p class="text-xs text-gray-400">Pro Communication Hub</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></span>
                            <span class="text-xs text-gray-300">Online</span>
                        </div>
                    </div>

                    <!-- SEARCH -->
                    <div class="p-4">
                        <div class="relative group">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search users..."
                                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm
                                focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-400 transition"
                            />

                            <svg
                                class="w-5 h-5 absolute left-4 top-3.5 text-gray-500 group-focus-within:text-cyan-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar px-2 pb-3">
                        <ChatList :search="search" />
                    </div>
                </aside>

                <main class="flex-1 flex flex-col bg-black/10">
                    <div class="h-[72px] flex items-center justify-between px-6 border-b border-white/10 backdrop-blur-xl bg-white/5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                                💬
                            </div>

                            <div>
                                <h2 class="font-semibold">Live Chat</h2>
                                <p class="text-xs text-gray-400">Secure • Fast • Real-time</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                🔔
                            </button>

                            <button class="w-10 h-10 rounded-xl bg-cyan-500 hover:bg-cyan-400 transition shadow-lg shadow-cyan-500/20">
                                📹
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <ChatBox />
                    </div>
                </main>

            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.35);
}
</style>
