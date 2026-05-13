<script setup>
import { ref, watch, nextTick, computed, onMounted } from 'vue'
import { useChatStore } from '@/stores/chat'

import 'emoji-picker-element'

const chat = useChatStore()

const text = ref('')
const box = ref(null)

const editingId = ref(null)
const editText = ref('')

const activeMessageId = ref(null)
const openMenuId = ref(null)

const showEmoji = ref(false)

const hasUser = computed(() => !!chat.selectedUser)

const addEmoji = (emoji) => {
    text.value += emoji.detail.unicode
}

const send = async () => {
    if (!text.value.trim() || !chat.selectedUser) return

    await chat.sendMessage(text.value)

    text.value = ''
    showEmoji.value = false
    activeMessageId.value = null
    openMenuId.value = null

    scrollBottom()
}

const selectMessage = (id) => {
    activeMessageId.value = activeMessageId.value === id ? null : id
    openMenuId.value = null
}

const toggleMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id
    activeMessageId.value = id
}

const startEdit = (m) => {
    editingId.value = m.id
    editText.value = m.message
    openMenuId.value = null
}

const cancelEdit = () => {
    editingId.value = null
    editText.value = ''
}

const saveEdit = async (m) => {
    if (!editText.value.trim()) return

    await chat.updateMessage(m.id, editText.value)

    m.message = editText.value
    editingId.value = null
    editText.value = ''
}

const deleteMsg = async (id) => {
    await chat.deleteMessage(id)
    openMenuId.value = null
    activeMessageId.value = null
}

const scrollBottom = () => {
    nextTick(() => {
        if (box.value) {
            box.value.scrollTop = box.value.scrollHeight
        }
    })
}

watch(() => chat.messages.length, scrollBottom)

onMounted(() => {
    scrollBottom()
})
</script>

<template>
    <div class="flex flex-col h-full bg-[#0b1220] text-white">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-4 border-b border-white/10 bg-black/30 backdrop-blur-xl">
            <div v-if="hasUser" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold">
                    {{ chat.selectedUser.name.charAt(0) }}
                </div>

                <div>
                    <h1 class="font-semibold">
                        {{ chat.selectedUser.name }}
                    </h1>

                    <p class="text-xs text-green-400">
                        ● Online
                    </p>
                </div>
            </div>

            <div v-else class="text-gray-400">
                Select user
            </div>

            <button
                v-if="hasUser"
                @click="chat.deleteConversation(chat.selectedUser.id)"
                class="text-red-400 hover:text-red-500 text-sm"
            >
                Delete all
            </button>
        </div>

        <!-- MESSAGES -->
        <div
            ref="box"
            class="flex-1 overflow-y-auto p-5 space-y-4"
            @click.self="activeMessageId = null; openMenuId = null"
        >
            <div
                v-for="m in chat.messages"
                :key="m.id"
                class="flex group"
                :class="m.is_me ? 'justify-end' : 'justify-start'"
            >
                <div
                    class="relative flex items-center gap-2 max-w-[80%]"
                    :class="m.is_me ? 'flex-row-reverse' : 'flex-row'"
                >
                    <!-- BUBBLE -->
                    <div
                        @click.stop="selectMessage(m.id)"
                        class="relative px-4 py-3 rounded-2xl text-sm shadow-lg break-words cursor-pointer transition"
                        :class="[
                            m.is_me
                                ? 'bg-blue-600 rounded-br-sm'
                                : 'bg-white/10 rounded-bl-sm border border-white/10',
                            activeMessageId === m.id ? 'ring-2 ring-white/20 scale-[1.01]' : ''
                        ]"
                    >
                        <!-- EDIT -->
                        <div
                            v-if="editingId === m.id"
                            class="space-y-2"
                            @click.stop
                        >
                            <input
                                v-model="editText"
                                @keyup.enter="saveEdit(m)"
                                class="w-full px-3 py-2 rounded-lg bg-slate-800 text-white outline-none border border-white/10"
                                autofocus
                            />

                            <div class="flex justify-end gap-2">
                                <button
                                    @click="cancelEdit"
                                    class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-xs"
                                >
                                    Annuler
                                </button>

                                <button
                                    @click="saveEdit(m)"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded text-xs"
                                >
                                    Save
                                </button>
                            </div>
                        </div>

                        <!-- MESSAGE -->
                        <div v-else>
                            <div>
                                {{ m.message }}
                            </div>

                            <div class="text-[10px] opacity-60 mt-1 text-right">
                                {{ m.created_at }}
                            </div>
                        </div>
                    </div>

                    <!-- BUTTON 3 POINTS, MISEHO REHEFA CLICK MESSAGE -->
                    <button
                        v-if="activeMessageId === m.id"
                        @click.stop="toggleMenu(m.id)"
                        class="w-8 h-8 rounded-full bg-slate-900/95 border border-white/10 hover:bg-slate-800 shadow-xl flex items-center justify-center text-lg leading-none transition"
                        title="Options"
                    >
                        ⋯
                    </button>

                    <!-- MENU PRO -->
                    <div
                        v-if="openMenuId === m.id"
                        class="absolute top-10 z-50 w-44 rounded-2xl bg-slate-950 border border-white/10 shadow-2xl overflow-hidden"
                        :class="m.is_me ? 'right-10' : 'left-10'"
                        @click.stop
                    >
                        <button
                            @click="startEdit(m)"
                            class="w-full flex items-center gap-2 text-left px-4 py-3 hover:bg-white/10 text-sm"
                        >
                            <span>✏️</span>
                            <span>Modifier</span>
                        </button>

                        <button
                            @click="deleteMsg(m.id)"
                            class="w-full flex items-center gap-2 text-left px-4 py-3 hover:bg-red-600 text-red-400 hover:text-white text-sm"
                        >
                            <span>🗑️</span>
                            <span>Supprimer</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- INPUT -->
        <div class="relative p-4 border-t border-white/10 bg-black/40">
            <!-- EMOJI PICKER -->
            <div
                v-if="showEmoji"
                class="absolute bottom-20 left-4 z-50"
            >
                <emoji-picker @emoji-click="addEmoji"></emoji-picker>
            </div>

            <div class="flex gap-2">
                <!-- EMOJI BUTTON -->
                <button
                    @click="showEmoji = !showEmoji"
                    class="w-14 flex items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-2xl transition disabled:opacity-50"
                    :disabled="!hasUser"
                >
                    😊
                </button>

                <!-- INPUT -->
                <input
                    v-model="text"
                    @keyup.enter="send"
                    :disabled="!hasUser"
                    placeholder="Type message..."
                    class="flex-1 px-4 py-3 rounded-xl bg-slate-800 text-white outline-none border border-white/10 focus:border-blue-500 disabled:opacity-50"
                />

                <!-- SEND -->
                <button
                    @click="send"
                    :disabled="!hasUser"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition disabled:opacity-50"
                >
                    Send
                </button>
            </div>
        </div>
    </div>
</template>
