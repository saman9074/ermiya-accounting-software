<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { useCurrency } from '@Core/composables/useCurrency';
import { onMounted } from 'vue';

const props = defineProps({
    transaction: Object,
    accounts: Array,
    categories: Array,
    payees: Array,
});

const { activeCurrency } = useCurrency();

const form = useForm({
    _method: 'put', // Important for file uploads with PUT/PATCH
    account_id: props.transaction.account_id,
    expense_category_id: props.transaction.expense_category_id,
    payee_id: props.transaction.payee_id,
    transaction_date: props.transaction.transaction_date.slice(0, 10),
    amount: Math.abs(props.transaction.amount),
    description: props.transaction.description,
    attachment: null, // New file to upload
});

onMounted(() => {
    if (activeCurrency.value && activeCurrency.value.divisor > 1) {
        form.amount = props.transaction.amount / activeCurrency.value.divisor;
    }
});

const onFileChange = (e) => {
    form.attachment = e.target.files[0];
};

const submit = () => {
    form.post(route('transactions.update', props.transaction.id)); // Use post because of file upload
};
</script>

<template>
    <Head :title="'ویرایش تراکنش ' + transaction.id" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ویرایش تراکنش</h2>
        </template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <div v-if="transaction.type === 'income'" class="mb-6 bg-yellow-100 p-4 rounded-md text-yellow-800 text-sm">
                            توجه: شما در حال ویرایش یک تراکنش دریافت هستید. ویرایش این مورد ممکن است با اسناد فروش تداخل ایجاد کند.
                        </div>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label>تاریخ</label>
                                    <input type="date" v-model="form.transaction_date" class="block w-full mt-1" required>
                                </div>
                                <div>
                                    <label>مبلغ ({{ activeCurrency.display_name }})</label>
                                    <input type="number" v-model="form.amount" class="block w-full mt-1" required>
                                </div>
                            </div>
                            <div>
                                <label>حساب</label>
                                <v-select dir="rtl" :options="accounts" label="name" :reduce="acc => acc.id" v-model="form.account_id"/>
                            </div>

                            <template v-if="transaction.type === 'expense'">
                                <div>
                                    <label>دسته‌بندی هزینه</label>
                                    <v-select dir="rtl" :options="categories" label="name" :reduce="cat => cat.id" v-model="form.expense_category_id"/>
                                </div>
                                <div>
                                    <label>طرف حساب</label>
                                    <v-select dir="rtl" :options="payees" label="name" :reduce="p => p.id" v-model="form.payee_id"/>
                                </div>
                            </template>

                            <div>
                                <label>توضیحات</label>
                                <textarea v-model="form.description" rows="3" class="block w-full mt-1"></textarea>
                            </div>
                            <div>
                                <label>تغییر فایل ضمیمه (اختیاری)</label>
                                <a v-if="transaction.attachment && !form.attachment" :href="`/storage/${transaction.attachment}`" target="_blank" class="text-sm text-blue-600 block mb-2 hover:underline">مشاهده فایل فعلی</a>
                                <input type="file" @change="onFileChange" class="block w-full mt-1 text-sm">
                            </div>

                            <div class="mt-8 flex justify-end gap-4">
                                <Link :href="route('transactions.index')">انصراف</Link>
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-md">
                                    به‌روزرسانی
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
