<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    success: String,
});

const deleteCategory = (id) => {
    if (confirm('آیا از حذف این دسته بندی مطمئن هستید؟')) {
        router.delete(route('expense-categories.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="دسته‌بندی هزینه‌ها" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت دسته‌بندی هزینه‌ها</h2>
                <Link :href="route('expense-categories.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    ایجاد دسته جدید
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
                    {{ success }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام دسته</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">توضیحات</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categories" :key="category.id">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ category.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                    <Link :href="route('expense-categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                    <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 mr-4">حذف</button>
                                </td>
                            </tr>
                            <tr v-if="!categories.length">
                                <td class="px-6 py-4 text-center" colspan="3">هیچ دسته‌بندی هزینه‌ای یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
