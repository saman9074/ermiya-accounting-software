<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    units: Array,
});

const deleteUnit = (id) => {
    if (confirm('آیا از حذف این واحد مطمئن هستید؟')) {
        router.delete(route('units.destroy', id));
    }
};
</script>

<template>
    <Head title="مدیریت واحدها" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت واحدها</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <Link :href="route('units.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                افزودن واحد جدید
                            </Link>
                        </div>
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                            <tr>
                                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">نام</th>
                                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">مخفف</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700">
                            <tr v-for="unit in units" :key="unit.id" class="border-b">
                                <td class="py-3 px-4">{{ unit.name }}</td>
                                <td class="py-3 px-4">{{ unit.abbreviation }}</td>
                                <td>
                                    <Link :href="route('units.edit', unit.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                                    <button @click="deleteUnit(unit.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                                </td>
                            </tr>
                            <tr v-if="units.length === 0">
                                <td colspan="3" class="text-center py-4">هیچ واحدی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
