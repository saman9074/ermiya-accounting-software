<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

defineProps({
    accounts: Array,
    categories: Array,
    payees: Array,
});

const form = useForm({
    account_id: null,
    expense_category_id: null,
    payee_id: null,
    transaction_date: new Date().toISOString().slice(0, 10),
    amount: '',
    description: '',
    attachment: null,
});

const onFileChange = (e) => {
    form.attachment = e.target.files[0];
};

const submit = () => {
    form.post(route('payments.store'), {
        forceFormData: true, // Important for file uploads
    });
};
</script>

<template>
    <Head title="ثبت هزینه / پرداخت" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ثبت هزینه / پرداخت جدید</h2>
        </template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="transaction_date">تاریخ</label>
                                    <input id="transaction_date" type="date" v-model="form.transaction_date" class="block w-full mt-1" required>
                                </div>
                                <div>
                                    <label for="amount">مبلغ</label>
                                    <input id="amount" type="number" v-model="form.amount" class="block w-full mt-1" required placeholder="مبلغ به ریال">
                                    <div v-if="form.errors.amount" class="text-sm text-red-600 mt-1">{{ form.errors.amount }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="account_id">پرداخت از حساب</label>
                                    <v-select dir="rtl" :options="accounts" label="name" :reduce="acc => acc.id" v-model="form.account_id" placeholder="انتخاب کنید..."/>
                                    <div v-if="form.errors.account_id" class="text-sm text-red-600 mt-1">{{ form.errors.account_id }}</div>
                                </div>
                                <div>
                                    <label for="expense_category_id">دسته‌بندی هزینه</label>
                                    <v-select dir="rtl" :options="categories" label="name" :reduce="cat => cat.id" v-model="form.expense_category_id" placeholder="انتخاب کنید..."/>
                                    <div v-if="form.errors.expense_category_id" class="text-sm text-red-600 mt-1">{{ form.errors.expense_category_id }}</div>
                                </div>
                            </div>

                            <div>
                                <label for="payee_id">طرف حساب (اختیاری)</label>
                                <v-select dir="rtl" :options="payees" label="name" :reduce="p => p.id" v-model="form.payee_id" placeholder="انتخاب کنید..."/>
                            </div>

                            <div>
                                <label for="description">توضیحات</label>
                                <textarea id="description" v-model="form.description" rows="3" class="block w-full mt-1"></textarea>
                            </div>

                            <div>
                                <label for="attachment">ضمیمه کردن فایل (اختیاری)</label>
                                <input id="attachment" type="file" @change="onFileChange" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <div v-if="form.errors.attachment" class="text-sm text-red-600 mt-1">{{ form.errors.attachment }}</div>
                            </div>

                            <div class="mt-8 flex justify-end gap-4">
                                <Link :href="route('transactions.index')" class="text-gray-600">انصراف</Link>
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-md">
                                    ثبت پرداخت
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
