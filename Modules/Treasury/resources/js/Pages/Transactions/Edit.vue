<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    transaction: Object,
    accounts: Array,
});

const form = useForm({
    account_id: props.transaction.account_id,
    amount: props.transaction.amount,
    transaction_date: new Date(props.transaction.transaction_date).toISOString().slice(0, 10),
    description: props.transaction.description,
});

const submit = () => {
    form.put(route('transactions.update', props.transaction.id));
};
</script>

<template>
    <Head title="ویرایش تراکنش" />

    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">ویرایش تراکنش #{{ transaction.id }}</h1>
        </div>

        <div class="max-w-2xl">
            <div class="card">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="amount" class="block font-medium text-sm text-gray-700">مبلغ</label>
                        <input v-model="form.amount" id="amount" type="number" step="any" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.amount" class="text-red-600 text-sm mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <label for="account_id" class="block font-medium text-sm text-gray-700">حساب</label>
                        <select v-model="form.account_id" id="account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.name }}</option>
                        </select>
                        <div v-if="form.errors.account_id" class="text-red-600 text-sm mt-1">{{ form.errors.account_id }}</div>
                    </div>

                    <div>
                        <label for="transaction_date" class="block font-medium text-sm text-gray-700">تاریخ</label>
                        <input v-model="form.transaction_date" id="transaction_date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.transaction_date" class="text-red-600 text-sm mt-1">{{ form.errors.transaction_date }}</div>
                    </div>

                    <div>
                        <label for="description" class="block font-medium text-sm text-gray-700">توضیحات</label>
                        <textarea v-model="form.description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t mt-6">
                        <Link :href="route('transactions.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره تغییرات</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
