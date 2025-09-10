<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@Core/Components/ApplicationLogo.vue'; // <-- Import the new component

// State for mobile sidebar
const sidebarOpen = ref(false);

</script>

<template>
    <div class="min-h-screen bg-brand-bg text-slate-800">
        <!-- Header -->
        <header class="fixed inset-x-0 top-0 z-40 bg-brand-header text-white shadow-md">
            <div class="mx-auto flex h-16 max-w-full items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="flex h-10 w-10 items-center justify-center rounded-md hover:bg-white/10 md:hidden" aria-label="Toggle menu">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <!-- Logo and Title -->
                    <Link :href="route('dashboard')" class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-md bg-white/10 p-1.5">
                            <ApplicationLogo />
                        </div>
                        <div class="leading-tight">
                            <div class="text-xs/6 opacity-90">سامانه</div>
                            <div class="text-lg font-semibold tracking-tight">نرم‌افزار حسابداری</div>
                        </div>
                    </Link>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Search Bar -->
                    <div class="hidden sm:block">
                        <label class="relative block">
                            <input aria-label="جستجو" placeholder="جستجو..." class="w-60 rounded-md border border-white/10 bg-white/5 py-2 pr-10 pl-3 text-sm placeholder:text-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
                            <svg class="absolute left-2 top-1/2 -translate-y-1/2 h-4 w-4 text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 21l-4.35-4.35" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="11" cy="11" r="6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </label>
                    </div>
                    <!-- User Profile -->
                    <div class="flex items-center gap-2 rounded-full bg-white/10 px-2 py-1">
                        <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-sm font-bold">
                            {{ $page.props.auth.user.name.substring(0, 2) }}
                        </div>
                        <span class="hidden text-sm font-medium sm:block">{{ $page.props.auth.user.name }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar (Right) -->
        <aside :class="['fixed top-16 bottom-0 right-0 z-30 w-64 transform bg-brand-header text-white shadow-xl transition-transform duration-300 md:translate-x-0', { 'translate-x-0': sidebarOpen, 'translate-x-full': !sidebarOpen }]" aria-label="Main Navigation">
            <nav class="flex h-full flex-col gap-1.5 overflow-y-auto p-4">
                <Link :href="route('dashboard')" :class="['sidebar-item', { 'active': $page.component.startsWith('Core::') }]">
                    <span>داشبورد</span>
                </Link>

                <p class="px-3 pt-4 pb-2 text-xs font-semibold uppercase text-white/50">مدیریت</p>
                <Link :href="route('persons.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Persons::') }]">
                    <span>اشخاص</span>
                </Link>
                <Link :href="route('products.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Inventory::Products') }]">
                    <span>کالاها</span>
                </Link>
                <Link :href="route('categories.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Inventory::Categories') }]">
                    <span>دسته‌بندی‌ها</span>
                </Link>
                <Link :href="route('units.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Inventory::Units') }]">
                    <span>واحدها</span>
                </Link>

                <p class="px-3 pt-4 pb-2 text-xs font-semibold uppercase text-white/50">عملیات</p>
                <Link :href="route('invoices.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Sales::') }]">
                    <span>فاکتور ها</span>
                </Link>

                <Link :href="route('accounts.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Treasury::Accounts') }]">
                    <span>حساب ها</span>
                </Link>
                <Link :href="route('transactions.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Treasury::Transactions') }]">
                    <span>لیست تراکنش‌ها</span>
                </Link>

                <p class="px-3 pt-4 pb-2 text-xs font-semibold uppercase text-white/50">تنظیمات</p>
                <Link :href="route('financial-years.index')" :class="['sidebar-item', { 'active': $page.component.startsWith('Core::FinancialYears') }]">
                    <span>سال مالی</span>
                </Link>
            </nav>
        </aside>

        <!-- Sidebar backdrop for mobile -->
        <div v-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/30 backdrop-blur-sm md:hidden" />

        <!-- Main content -->
        <main class="pt-16 md:pr-64">
            <div class="p-4 sm:p-6 lg:p-8">
                <slot />
            </div>
        </main>
    </div>
</template>

