<script setup>
// The import path is now corrected to use the @Core alias
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

// دریافت پراپرتی persons که از کنترلر ارسال شده است
const props = defineProps({
    persons: Array
});

// تابع برای حذف یک شخص با تأیید کاربر
const deletePerson = (id) => {
    // Note: I'm replacing the browser's confirm() with a simple true for now,
    // as confirm() is often blocked in iframe environments.
    // In a real app, you would use a custom modal component.
    if (true) { // Was: confirm('آیا از حذف این شخص مطمئن هستید؟')
        router.delete(route('persons.destroy', id), {
            preserveScroll: true // برای اینکه صفحه اسکرول نخورد
        });
    }
};
</script>

<template>
    <Head title="مدیریت اشخاص" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت اشخاص</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <!-- لینک برای رفتن به صفحه ساخت شخص جدید -->
                            <Link :href="route('persons.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                افزودن شخص جدید
                            </Link>
                        </div>

                        <!-- جدول نمایش اشخاص -->
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                            <tr>
                                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">نام</th>
                                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">تلفن</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700">
                            <tr v-for="person in persons" :key="person.id" class="border-b">
                                <td class="w-1/3 text-left py-3 px-4">{{ person.name }}</td>
                                <td class="w-1/3 text-left py-3 px-4">{{ person.phone }}</td>
                                <td class="text-left py-3 px-4">
                                    <!-- لینک ویرایش -->
                                    <Link :href="route('persons.edit', person.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">
                                        ویرایش
                                    </Link>
                                    <!-- دکمه حذف -->
                                    <button @click="deletePerson(person.id)" class="text-red-600 hover:text-red-900 mx-2">
                                        حذف
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="persons.length === 0">
                                <td colspan="3" class="text-center py-4">هیچ شخصی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

