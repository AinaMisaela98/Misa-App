<script setup>
import { computed, onMounted } from 'vue'
import { useChatStore } from '@/stores/chat'

const props = defineProps({
    search: {
        type: String,
        default: '',
    },
})

const chat = useChatStore()

onMounted(() => {
    chat.loadedUsers = false
    chat.fetchUsers(true)
})

const filteredUsers = computed(() => {
    const keyword = props.search.toLowerCase().trim()

    if (!keyword) {
        return chat.users
    }

    return chat.users.filter((u) => {
        return (
            u.name?.toLowerCase().includes(keyword) ||
            u.email?.toLowerCase().includes(keyword) ||
            u.role?.toLowerCase().includes(keyword)
        )
    })
})
</script>

<template>
    <div class="h-full flex flex-col bg-gradient-to-b from-slate-950 via-slate-900 to-black text-white">
        <!-- HEADER -->
        <div class="p-4 border-b border-white/10 bg-black/30 backdrop-blur-xl">
            <h1 class="text-xl font-bold tracking-wide">💬 Messages</h1>
            <p class="text-xs text-gray-400">Select user to start chat</p>
        </div>

        <!-- USERS LIST -->
        <div class="flex-1 overflow-y-auto">
            <div
                v-for="u in filteredUsers"
                :key="u.id"
                @click="chat.selectUser(u)"
                class="flex items-center gap-3 px-4 py-3 cursor-pointer transition rounded-lg mx-2 my-1 hover:bg-white/5"
                :class="chat.selectedUser?.id === u.id ? 'bg-white/10' : ''"
            >
                <!-- AVATAR -->
                <div class="relative">
                    <img
                        v-if="u.photo"
                        :src="u.photo.startsWith('http') ? u.photo : `/storage/${u.photo}`"
                        class="w-11 h-11 rounded-full object-cover border border-white/10 shadow-md"
                        alt="Photo"
                    />

                    <div
                        v-else
                        class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center font-bold shadow-md"
                    >
                        {{ u.name?.charAt(0).toUpperCase() }}
                    </div>

                    <!-- ONLINE DOT -->
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-black rounded-full animate-pulse"></span>
                </div>

                <!-- INFO -->
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white truncate">
                        {{ u.name }}
                    </p>

                    <p class="text-xs text-gray-400 truncate">
                        {{ chat.selectedUser?.id === u.id ? 'Active chat' : 'Click to chat' }}
                    </p>
                </div>

                <!-- RIGHT STATUS -->
                <div class="text-xs text-green-400">
                    ●
                </div>
            </div>

            <!-- EMPTY SEARCH -->
            <div
                v-if="chat.users.length && !filteredUsers.length"
                class="p-4 text-center text-gray-500"
            >
                Aucun utilisateur trouvé
            </div>

            <!-- LOADING -->
            <div
                v-if="!chat.users.length"
                class="p-4 text-center text-gray-500"
            >
                Loading users...
            </div>
        </div>
    </div>
</template>
