<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    invoice: Object,
    accounts: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    invoice_id: props.invoice.id,
    account_id: props.accounts.length > 0 ? props.accounts[0].id : null,
    amount: props.invoice.remaining_amount,
    transaction_date: new Date().toISOString().slice(0, 10), // Today's date
    description: `پرداخت بابت فاکتور شماره ${props.invoice.id}`,
});

const closeModal = () => {
    emit('close');
};

const remainingAmount = computed(() => {
    return props.invoice.total_amount - props.invoice.paid_amount;
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        form.amount = remainingAmount.value;
        form.errors = {}; // Clear previous errors
    }
});

const submit = () => {
    // Use the NEW route name to submit the form
    form.post(route('invoices.transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="emit('close')">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900">ثبت دریافت وجه برای فاکتور #{{ invoice.id }}</h3>
                <p class="text-sm text-gray-500 mt-1">مبلغ باقیمانده: {{ new Number(remainingAmount).toLocaleString('fa-IR') }} تومان</p>

                <form @submit.prevent="submit" class="mt-6 space-y-4">
                    <div>
                        <label for="amount" class="block font-medium text-sm text-gray-700">مبلغ دریافتی</label>
                        <input v-model="form.amount" id="amount" type="number" step="any" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.amount" class="text-red-600 text-sm mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <label for="account_id" class="block font-medium text-sm text-gray-700">واریز به حساب</label>
                        <select v-model="form.account_id" id="account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.name }} ({{ account.type === 'cash' ? 'صندوق' : 'بانک' }})</option>
                        </select>
                        <div v-if="form.errors.account_id" class="text-red-600 text-sm mt-1">{{ form.errors.account_id }}</div>
                    </div>

                    <div>
                        <label for="transaction_date" class="block font-medium text-sm text-gray-700">تاریخ دریافت</label>
                        <input v-model="form.transaction_date" id="transaction_date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.transaction_date" class="text-red-600 text-sm mt-1">{{ form.errors.transaction_date }}</div>
                    </div>

                    <div>
                        <label for="description" class="block font-medium text-sm text-gray-700">توضیحات</label>
                        <textarea v-model="form.description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t mt-6">
                        <button type="button" @click="emit('close')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</button>
                        <button type="submit" :disabled="form.processing" class="btn-primary">ثبت دریافت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
