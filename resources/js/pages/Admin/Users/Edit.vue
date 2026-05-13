<script setup>
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['cancel', 'saved'])

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    role: props.user.role,
})

watch(
    () => props.user,
    (newUser) => {
        form.name = newUser.name
        form.email = newUser.email
        form.password = ''
        form.role = newUser.role
        form.clearErrors()
    },
    { deep: true }
)

const submit = () => {
    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('password')
            emit('saved')
        }
    })
}
</script>

<template>
    <div class="rounded-3xl bg-white p-6 shadow border border-amber-100">
        <h3 class="text-2xl font-black mb-1">Modifier utilisateur</h3>
        <p class="text-slate-500 mb-6">Modification du compte : {{ user.email }}</p>

        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="font-bold">Nom</label>
                <input v-model="form.name" class="mt-2 w-full rounded-xl border px-4 py-3" />
                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="font-bold">Email</label>
                <input v-model="form.email" type="email" class="mt-2 w-full rounded-xl border px-4 py-3" />
                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="font-bold">Nouveau mot de passe</label>
                <input v-model="form.password" type="password" placeholder="Laisser vide si inchangé" class="mt-2 w-full rounded-xl border px-4 py-3" />
                <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="font-bold">Rôle</label>
                <select v-model="form.role" class="mt-2 w-full rounded-xl border px-4 py-3">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <p v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</p>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                <button
                    type="button"
                    @click="emit('cancel')"
                    class="rounded-xl bg-slate-200 px-5 py-3 font-bold"
                >
                    Annuler
                </button>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-amber-500 px-5 py-3 text-white font-bold hover:bg-amber-600"
                >
                    Sauver modification
                </button>
            </div>
        </form>
    </div>
</template>
