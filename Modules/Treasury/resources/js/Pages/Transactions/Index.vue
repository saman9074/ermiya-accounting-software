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

const transactionTypeClass = (type) => {
    return type === 'income' ? 'text-green-600' : 'text-red-600';
};
const transactionTypeText = (type) => {
    return type === 'income' ? 'دریافت' : 'پرداخت';
};

</script>

<template>
    <Head title="لیست تراکنش‌ها" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">لیست تراکنش‌ها</h1>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نوع</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مبلغ</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">حساب</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مربوط به</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="tx in transactions" :key="tx.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(tx.transaction_date) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold" :class="transactionTypeClass(tx.type)">
                        {{ transactionTypeText(tx.type) }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatCurrency(tx.amount) }} تومان</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ tx.account.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link v-if="tx.transactionable" :href="route('invoices.show', tx.transactionable.id)">
                            فاکتور #{{ tx.transactionable.id }}
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
    </AuthenticatedLayout>
</template>
