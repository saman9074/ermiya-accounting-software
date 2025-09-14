<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    payees: Array,
    success: String,
});

const deletePayee = (id) => {
    if (confirm('آیا از حذف این طرف حساب مطمئن هستید؟')) {
        router.delete(route('payees.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="طرف حساب‌ها" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت طرف حساب‌ها</h2>
                <Link :href="route('payees.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    ایجاد طرف حساب جدید
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
                                <th class="px-6 py-3 text-right">نام</th>
                                <th class="px-6 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="payee in payees" :key="payee.id">
                                <td class="px-6 py-4 font-medium">{{ payee.name }}</td>
                                <td class="px-6 py-4 text-left">
                                    <Link :href="route('payees.edit', payee.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                    <button @click="deletePayee(payee.id)" class="text-red-600 hover:text-red-900 mr-4">حذف</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
