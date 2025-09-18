<script setup>
import { watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import JalaliDatePicker from '@Core/Components/JalaliDatePicker.vue';
import { useCurrency } from '@Core/composables/useCurrency';

const props = defineProps({
    show: Boolean,
    invoice: Object,
    accounts: Array,
    personCredit: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['close']);
const { activeCurrency } = useCurrency();

const form = useForm({
    invoice_id: null,
    account_id: null,
    amount: 0,
    apply_credit: 0,
    transaction_date: new Date().toISOString().slice(0, 10),
    description: '',
    type: 'income',
});

const remainingBalance = computed(() => {
    return props.invoice ? (props.invoice.total_amount - props.invoice.paid_amount) : 0;
});

watch(() => props.show, (newVal) => {
    if (newVal && props.invoice) {
        form.invoice_id = props.invoice.id;
        // مقدار اولیه پرداخت را برابر با مانده فاکتور قرار بده
        form.amount = remainingBalance.value;
        form.apply_credit = 0;
        form.description = `بابت فاکتور شماره ${props.invoice.id}`;
        form.errors = {};
    }
});

const submit = () => {
    // نام روت را به چیزی که خودتان گفتید صحیح است تغییر دادم
    form.post(route('invoices.transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            form.reset();
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">دریافت وجه برای فاکتور #{{ invoice.id }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                مبلغ قابل دریافت: {{ Number(remainingBalance).toLocaleString() }} {{ activeCurrency.display_name }}
            </p>

            <div v-if="personCredit > 0" class="mt-4 p-3 bg-green-100 text-green-800 rounded-md text-sm">
                مشتری مبلغ {{ Number(personCredit).toLocaleString() }} {{ activeCurrency.display_name }} اعتبار دارد.
            </div>

            <form @submit.prevent="submit">
                <div class="mt-4">
                    <label for="transaction_date">تاریخ دریافت</label>
                    <JalaliDatePicker id="transaction_date" v-model="form.transaction_date" class="mt-1 block w-full" required />
                    <div v-if="form.errors.transaction_date" class="text-sm text-red-600 mt-1">{{ form.errors.transaction_date }}</div>
                </div>

                <div v-if="personCredit > 0" class="mt-4">
                    <label for="apply_credit">استفاده از اعتبار</label>
                    <input id="apply_credit" type="number" v-model.number="form.apply_credit" :max="Math.min(personCredit, remainingBalance)" class="mt-1 block w-full">
                    <div v-if="form.errors.apply_credit" class="text-sm text-red-600 mt-1">{{ form.errors.apply_credit }}</div>
                </div>

                <div class="mt-4">
                    <label for="amount">مبلغ دریافتی (نقد/بانک)</label>
                    <input id="amount" type="number" v-model.number="form.amount" class="mt-1 block w-full">
                    <div v-if="form.errors.amount" class="text-sm text-red-600 mt-1">{{ form.errors.amount }}</div>
                </div>

                <div class="mt-4">
                    <label for="account_id">واریز به حساب</label>
                    <select id="account_id" v-model="form.account_id" class="mt-1 block w-full">
                        <option :value="null">انتخاب کنید</option>
                        <option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.name }}</option>
                    </select>
                    <div v-if="form.errors.account_id" class="text-sm text-red-600 mt-1">{{ form.errors.account_id }}</div>
                </div>

                <div class="mt-4">
                    <label for="description">توضیحات</label>
                    <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full"></textarea>
                    <div v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="emit('close')">انصراف</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        ثبت دریافت
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
