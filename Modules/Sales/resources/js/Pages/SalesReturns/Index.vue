<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    salesReturns: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('fa-IR').format(number);
};

const deleteReturn = (id) => {
    if (confirm('آیا از حذف این سند اطمینان دارید؟ تمام عملیات مرتبط معکوس خواهد شد.')) {
        router.delete(route('sales_returns.destroy', id));
    }
};
</script>

<template>
    <Head title="اسناد برگشت از فروش" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست اسناد برگشت از فروش</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right">شماره سند</th>
                                <th class="px-6 py-3 text-right">تاریخ</th>
                                <th class="px-6 py-3 text-right">مشتری</th>
                                <th class="px-6 py-3 text-right">فاکتور مرجع</th>
                                <th class="px-6 py-3 text-right">مبلغ کل</th>
                                <th class="px-6 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="sr in salesReturns.data" :key="sr.id">
                                <td class="px-6 py-4">{{ sr.id }}</td>
                                <td class="px-6 py-4">{{ formatDate(sr.return_date) }}</td>
                                <td class="px-6 py-4">{{ sr.person.name }}</td>
                                <td class="px-6 py-4">
                                    <Link :href="route('invoices.show', sr.invoice_id)" class="text-blue-600 hover:underline">
                                        {{ sr.invoice_id }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">{{ formatNumber(sr.total_amount) }}</td>
                                <td class="px-6 py-4 text-left">
                                    <button @click="deleteReturn(sr.id)" class="text-red-600 hover:text-red-900">حذف</button>
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
