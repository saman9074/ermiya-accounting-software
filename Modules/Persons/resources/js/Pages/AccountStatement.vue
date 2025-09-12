<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    person: Object,
    invoices: Array,
    transactions: Array,
    balance: Number,
    total_invoices: Number,
    total_paid: Number,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fa-IR', options);
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('fa-IR').format(number);
};
</script>

<template>
    <Head :title="'صورتحساب ' + person.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                صورتحساب: {{ person.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div><strong>کد:</strong> {{ person.id }}</div>
                            <div><strong>نام:</strong> {{ person.name }}</div>
                            <div><strong>تلفن:</strong> {{ person.phone }}</div>
                            <div><strong>آدرس:</strong> {{ person.address }}</div>
                        </div>

                        <h3 class="text-lg font-bold mb-4">لیست فاکتورها</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شماره فاکتور</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ صدور</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ کل</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">پرداخت شده</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مانده فاکتور</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="invoice in invoices" :key="invoice.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ invoice.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(invoice.issue_date) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatNumber(invoice.total_amount) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatNumber(invoice.paid_amount) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ formatNumber(invoice.total_amount - invoice.paid_amount) }}</td>
                                </tr>
                                <tr v-if="!invoices.length">
                                    <td class="px-6 py-4 text-center" colspan="5">هیچ فاکتوری یافت نشد.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 p-4 border-t border-gray-200">
                            <div class="grid grid-cols-3 gap-4 text-lg">
                                <div>جمع کل فاکتورها: <span class="font-bold">{{ formatNumber(total_invoices) }}</span></div>
                                <div>جمع کل پرداختی‌ها: <span class="font-bold text-green-600">{{ formatNumber(total_paid) }}</span></div>
                                <div>مانده نهایی حساب: <span class="font-bold text-red-600">{{ formatNumber(balance) }}</span></div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <Link :href="route('persons.index')" class="text-blue-600 hover:text-blue-900">
                                &larr; بازگشت به لیست اشخاص
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
