<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    transactions: Array,
    success: String,
});

const deleteTransaction = (id) => {
    if (confirm('آیا از حذف این تراکنش مطمئن هستید؟ این عمل غیرقابل بازگشت است و موجودی حساب‌ها را تغییر می‌دهد.')) {
        router.delete(route('transactions.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const formatCurrency = (amount) => {
    return new Number(amount).toLocaleString('fa-IR');
};

const transactionTypeClass = (type, amount) => {
    if (amount < 0) return 'text-orange-600'; // For credit notes
    return type === 'income' ? 'text-green-600' : 'text-red-600';
};

const transactionTypeText = (type, amount) => {
    if (amount < 0 && type === 'income') return 'اعتبار مشتری';
    if (type === 'income') return 'دریافت';
    if (type === 'expense') return 'پرداخت';
    return type;
};
</script>

<template>
    <Head title="لیست تراکنش‌ها" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست تراکنش‌ها</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">تاریخ</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">نوع</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">مبلغ</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">حساب</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">مربوط به</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="tx in transactions" :key="tx.id">
                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(tx.transaction_date) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold" :class="transactionTypeClass(tx.type, tx.amount)">
                                    {{ transactionTypeText(tx.type, tx.amount) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatCurrency(tx.amount) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ tx.account?.name || '---' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <span v-if="!tx.transactionable">---</span>
                                    <Link v-else-if="tx.transactionable_type.includes('Invoice')" :href="route('invoices.show', tx.transactionable.id)" class="text-blue-600 hover:underline">
                                        فاکتور #{{ tx.transactionable.id }}
                                    </Link>
                                    <Link v-else-if="tx.transactionable_type.includes('SalesReturn')" :href="route('invoices.show', tx.transactionable.invoice_id)" class="text-blue-600 hover:underline">
                                        برگشتی فاکتور #{{ tx.transactionable.invoice_id }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <Link :href="route('transactions.edit', tx.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                                    <button @click="deleteTransaction(tx.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                                </td>
                            </tr>
                            <tr v-if="transactions.length === 0">
                                <td colspan="6" class="text-center py-4 text-slate-500">هیچ تراکنشی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
