<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCurrency } from '@Core/composables/useCurrency';
const { formatCurrency } = useCurrency();

const props = defineProps({
    transactions: Object,
    filters: Object,
    success: String,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('transactions.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteTransaction = (id) => {
    if (confirm('آیا از حذف این تراکنش مطمئن هستید؟ این عمل موجودی حساب مربوطه را معکوس خواهد کرد.')) {
        router.delete(route('transactions.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => dateString ? new Date(dateString).toLocaleDateString('fa-IR') : '';
const transactionTypeClass = (tx) => {
    if (tx.amount < 0 && tx.type === 'income') return 'text-purple-600';
    return tx.type === 'income' ? 'text-green-600' : 'text-red-600';
};
const transactionTypeText = (tx) => {
    if (tx.amount < 0 && tx.type === 'income') return 'اعتبار مشتری';
    return tx.type === 'income' ? 'دریافت' : 'پرداخت';
};
</script>

<template>
    <Head title="لیست تراکنش‌ها" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست تراکنش‌ها</h2>
                <div>
                    <Link :href="route('payments.create')" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md text-sm ml-2">
                        پرداخت جدید
                    </Link>
                </div>
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
                    {{ success }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو در توضیحات یا مبلغ..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right">تاریخ</th>
                                <th class="px-4 py-3 text-right">نوع</th>
                                <th class="px-4 py-3 text-right">مبلغ</th>
                                <th class="px-4 py-3 text-right">حساب</th>
                                <th class="px-4 py-3 text-right">جزئیات</th>
                                <th class="px-4 py-3 text-right">ضمیمه</th>
                                <th class="px-4 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="tx in transactions.data" :key="tx.id">
                                <td class="px-4 py-3">{{ formatDate(tx.transaction_date) }}</td>
                                <td class="px-4 py-3 font-semibold" :class="transactionTypeClass(tx)">{{ transactionTypeText(tx) }}</td>
                                <td class="px-4 py-3">{{ formatCurrency(tx.amount) }}</td>
                                <td class="px-4 py-3">{{ tx.account?.name || '---' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div v-if="tx.type === 'expense'">
                                        <div>دسته: {{ tx.expense_category?.name || 'نامشخص' }}</div>
                                        <div v-if="tx.payee">به: {{ tx.payee.name }}</div>
                                    </div>
                                    <div v-else>
                                        <Link v-if="tx.transactionable" :href="route('invoices.show', tx.transactionable.invoice_id || tx.transactionable.id)" class="text-blue-600 hover:underline">
                                            سند #{{ tx.transactionable.id }}
                                        </Link>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a v-if="tx.attachment" :href="`/storage/${tx.attachment}`" target="_blank" class="text-blue-500 hover:underline">مشاهده</a>
                                    <span v-else>---</span>
                                </td>
                                <td class="px-4 py-3 text-left text-sm font-medium">
                                    <Link :href="route('transactions.edit', tx.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                    <button @click="deleteTransaction(tx.id)" class="text-red-600 hover:text-red-900 mr-4">حذف</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <Pagination :links="transactions.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
