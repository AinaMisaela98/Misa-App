<script setup>
import { ref, computed } from 'vue'

import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

import { Link, usePage } from '@inertiajs/vue3'

const showingNavigationDropdown = ref(false)

const page = usePage()

const user = computed(() => page.props.auth?.user ?? null)

const userName = computed(() => user.value?.name ?? 'Utilisateur')
const userEmail = computed(() => user.value?.email ?? '')
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">

            <!-- NAVBAR -->
            <nav class="border-b border-gray-200 bg-gray shadow-sm">

                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    <div class="flex h-16 justify-between">

                        <!-- LEFT -->
                        <div class="flex">

                            <!-- LOGO -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- MENU -->
                            <div
                                class="hidden sm:flex sm:items-center sm:ms-10 space-x-8"
                            >

                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>

                            </div>
                        </div>

                        <!-- USER DROPDOWN -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">

                            <div class="relative ms-3">

                                <Dropdown align="right" width="48">

                                    <!-- BUTTON -->
                                    <template #trigger>

                                        <span class="inline-flex rounded-md">

                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                                            >

                                                {{ userName }}

                                                <svg
                                                    class="ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>

                                            </button>

                                        </span>

                                    </template>

                                    <!-- DROPDOWN -->
                                    <template #content>

                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            👤 Mon Profil
                                        </DropdownLink>

                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            🚪 Déconnexion
                                        </DropdownLink>

                                    </template>

                                </Dropdown>

                            </div>

                        </div>

                        <!-- MOBILE BUTTON -->
                        <div class="-me-2 flex items-center sm:hidden">

                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 transition"
                            >

                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />

                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />

                                </svg>

                            </button>

                        </div>

                    </div>

                </div>

                <!-- MOBILE MENU -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >

                    <!-- MENU -->
                    <div class="space-y-1 pb-3 pt-2">

                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href="/cours">
                            Cours
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href="/chat">
                            Chat
                        </ResponsiveNavLink>

                    </div>

                    <!-- USER -->
                    <div class="border-t border-gray-200 pb-3 pt-4">

                        <div class="px-4">

                            <div class="text-base font-semibold text-gray-800">
                                {{ userName }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ userEmail }}
                            </div>

                        </div>

                        <!-- MOBILE LINKS -->
                        <div class="mt-3 space-y-1">

                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                            >
                                👤 Mon Profil
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                🚪 Déconnexion
                            </ResponsiveNavLink>

                        </div>

                    </div>

                </div>

            </nav>

            <!-- HEADER -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- CONTENT -->
            <main>
                <slot />
            </main>

        </div>
    </div>
</template>
