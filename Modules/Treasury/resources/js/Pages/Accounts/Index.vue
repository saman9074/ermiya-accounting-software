<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    accounts: Array,
    success: String,
});

const deleteAccount = (id) => {
    if (confirm('آیا از حذف این حساب مطمئن هستید؟')) {
        router.delete(route('accounts.destroy', id));
    }
};

const formatCurrency = (amount) => {
    return new Number(amount).toLocaleString('fa-IR');
};
</script>

<template>
    <Head title="صندوق و بانک" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">مدیریت صندوق و بانک</h1>
            <Link :href="route('accounts.create')" class="btn-primary">تعریف حساب جدید</Link>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام حساب</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نوع</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">موجودی اولیه</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="account in accounts" :key="account.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ account.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ account.type === 'cash' ? 'صندوق' : 'بانک' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatCurrency(account.initial_balance) }} تومان</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('accounts.edit', account.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                        <button @click="deleteAccount(account.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                    </td>
                </tr>
                <tr v-if="accounts.length === 0">
                    <td colspan="4" class="text-center py-4 text-slate-500">هیچ حسابی یافت نشد.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
