<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    invoices: Object, // <-- اصلاحیه ۱: نوع پراپ به Object تغییر کرد
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const formatNumber = (number) => {
    if (number === null || isNaN(number)) return 0;
    return new Intl.NumberFormat('fa-IR').format(number);
};
</script>

<template>
    <Head title="لیست فاکتورها" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست فاکتورهای فروش</h2>
                <Link :href="route('invoices.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    فاکتور جدید
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right">#</th>
                                <th class="px-6 py-3 text-right">مشتری</th>
                                <th class="px-6 py-3 text-right">تاریخ</th>
                                <th class="px-6 py-3 text-right">مبلغ کل</th>
                                <th class="px-6 py-3 text-right">وضعیت</th>
                                <th class="px-6 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="invoice in invoices.data" :key="invoice.id">
                                <td class="px-6 py-4">{{ invoice.id }}</td>
                                <td class="px-6 py-4">{{ invoice.person.name }}</td>
                                <td class="px-6 py-4">{{ formatDate(invoice.issue_date) }}</td>
                                <td class="px-6 py-4">{{ formatNumber(invoice.total_amount) }}</td>
                                <td class="px-6 py-4">{{ invoice.translated_status }}</td>                                <td class="px-6 py-4 text-left">
                                    <Link :href="route('invoices.show', invoice.id)" class="text-indigo-600 hover:text-indigo-900">
                                        مشاهده
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!invoices.data.length">
                                <td class="px-6 py-4 text-center" colspan="6">هیچ فاکتوری یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
