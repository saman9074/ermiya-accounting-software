<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';
// از نام مستعار برای آدرس‌دهی استفاده می‌کنیم
import CategoryRow from '@Inventory/Pages/Categories/Components/CategoryRow.vue';

defineProps({
    categories: Array,
});
</script>

<template>
    <Head title="مدیریت دسته‌بندی‌ها" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت دسته‌بندی‌ها</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4">
                            <Link :href="route('categories.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                افزودن دسته‌بندی جدید
                            </Link>
                        </div>
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                            <tr>
                                <th class="w-2/3 text-left py-3 px-4 uppercase font-semibold text-sm">نام</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700">
                            <template v-if="categories.length > 0">
                                <!-- از کامپوننت ایمپورت شده استفاده می‌کنیم -->
                                <CategoryRow v-for="category in categories" :key="category.id" :category="category" />
                            </template>
                            <tr v-else>
                                <td colspan="2" class="text-center py-4">هیچ دسته‌بندی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

