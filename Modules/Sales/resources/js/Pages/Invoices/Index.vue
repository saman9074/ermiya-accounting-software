<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    invoices: {
        type: Array,
        default: () => [],
    },
    success: String,
});

// Helper to format date
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fa-IR', options);
};

// Helper to format currency
const formatCurrency = (amount) => {
    return new Number(amount).toLocaleString('fa-IR');
};
</script>

<template>
    <Head title="لیست فاکتورها" />
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-slate-800">لیست فاکتورها</h1>
            <Link :href="route('invoices.create')" class="btn-primary">صدور فاکتور جدید</Link>
        </div>

        <!-- Success Message -->
        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">شماره فاکتور</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مشتری</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مبلغ کل</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="invoice in invoices" :key="invoice.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">INV-{{ invoice.id }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ invoice.person.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(invoice.invoice_date) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatCurrency(invoice.total_amount) }} تومان</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('invoices.show', invoice.id)" class="text-indigo-600 hover:text-indigo-900">
                            مشاهده
                        </Link>
                    </td>
                </tr>
                <tr v-if="invoices.length === 0">
                    <td colspan="5" class="text-center py-4 text-sm text-gray-500">هیچ فاکتوری یافت نشد.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

