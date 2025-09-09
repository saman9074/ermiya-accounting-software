<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-start h-16">
                    <div class="flex items-center">
                        <span class="font-bold text-lg">حسابداری اِرمیا</span>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <Link :href="route('dashboard')" :class="{ 'border-indigo-400 text-gray-900': $page.component === 'Core::Dashboard', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': $page.component !== 'Core::Dashboard' }" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            داشبورد
                        </Link>
                        <Link :href="route('persons.index')" :class="{ 'border-indigo-400 text-gray-900': $page.component.startsWith('Persons::'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page.component.startsWith('Persons::') }" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            لیست اشخاص
                        </Link>
                        <Link :href="route('persons.create')" :class="{ 'border-indigo-400 text-gray-900': $page.component === 'Persons::create', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': $page.component !== 'Persons::create' }" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            افزودن شخص
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

