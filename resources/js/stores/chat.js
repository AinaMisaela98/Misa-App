import { defineStore } from 'pinia'
import axios from 'axios'

export const useChatStore = defineStore('chat', {

    state: () => ({
        users: [],
        messages: [],
        selectedUser: null,
        loadedUsers: false,
        loadingMessages: false,
    }),

/* =========================
   USERS (ANTI-DUPLICATE FIX)
========================= */
    actions: {

        async fetchUsers(force = false) {

            if (this.loadedUsers && !force) return

            try {
                const { data } = await axios.get('/chat/users')

                // 🧠 ULTRA SAFE: remove duplicates by ID
                const map = new Map()

                data.forEach(u => {
                    map.set(u.id, {
                        ...map.get(u.id),
                        ...u
                    })
                })

                this.users = Array.from(map.values())
                this.loadedUsers = true

            } catch (e) {
                console.error('users error', e)
            }
        },

/* =========================
   SELECT USER
========================= */
        async selectUser(user) {

            if (this.selectedUser?.id === user.id) return

            this.selectedUser = user
            this.messages = []

            await this.fetchMessages()
        },

/* =========================
   MESSAGES (SAFE PUSH)
========================= */
        async fetchMessages() {

            if (!this.selectedUser) return

            this.loadingMessages = true

            try {
                const { data } = await axios.get(
                    `/chat/messages/${this.selectedUser.id}`
                )

                // 🧠 remove duplicates messages also (IMPORTANT)
                const map = new Map()

                data.forEach(m => {
                    map.set(m.id, m)
                })

                this.messages = Array.from(map.values())

            } catch (e) {
                console.error('messages error', e)
            } finally {
                this.loadingMessages = false
            }
        },

/* =========================
   SEND MESSAGE (SAFE PUSH)
========================= */
        async sendMessage(text) {

            if (!text?.trim() || !this.selectedUser) return

            const { data } = await axios.post('/chat/send', {
                receiver_id: this.selectedUser.id,
                message: text
            })

            // 🧠 prevent duplicate push
            if (!this.messages.find(m => m.id === data.id)) {
                this.messages.push(data)
            }
        },

/* =========================
   DELETE MESSAGE
========================= */
        async deleteMessage(id) {

            try {
                await axios.delete(`/chat/message/${id}`)

                this.messages = this.messages.filter(m => m.id !== id)

            } catch (e) {
                console.error('delete message error', e)
            }
        },

/* =========================
   UPDATE MESSAGE
========================= */
        async updateMessage(id, message) {

            try {
                const { data } = await axios.put(
                    `/chat/message/${id}`,
                    { message }
                )

                const msg = this.messages.find(m => m.id === id)

                if (msg) {
                    msg.message = data.message
                }

            } catch (e) {
                console.error('update error', e)
            }
        },

/* =========================
   DELETE CONVERSATION
========================= */
        async deleteConversation(userId) {

            try {
                await axios.delete(
                    `/chat/conversation/${userId}`
                )

                this.messages = []

            } catch (e) {
                console.error('conversation delete error', e)
            }
        }
    }
})