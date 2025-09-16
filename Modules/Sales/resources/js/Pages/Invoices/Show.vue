<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ReceivePaymentModal from '@Sales/Components/ReceivePaymentModal.vue';
import { useCurrency } from '@Core/composables/useCurrency';

const { formatCurrency, activeCurrency } = useCurrency();

import { computed } from 'vue';

const isPayable = computed(() => {
    const remainingAmount = props.invoice.total_amount - props.invoice.paid_amount;
    // با یک تلورانس کوچک برای خطاهای ممیز شناور مقایسه شود
    return remainingAmount > 0.001 && props.invoice.status !== 'returned';
});

// این دکمه باید فعال باشد اگر وضعیت فاکتور 'مرجوعی' نباشد
const isReturnable = computed(() => {
    return props.invoice.status !== 'returned';
});
const props = defineProps({
    invoice: Object,
    companySettings: Object,
    accounts: Array,
});

const isPaymentModalOpen = ref(false);

const openPaymentModal = () => {
    isPaymentModalOpen.value = true;
};

const closePaymentModal = () => {
    isPaymentModalOpen.value = false;
};

const formatNumber = (number) => {
    if (number === null || typeof number === 'undefined' || isNaN(number)) {
        return formatNumber(0);
    }
    return new Intl.NumberFormat('fa-IR').format(number);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};

const printInvoice = () => {
    window.print();
};
</script>

<template>
    <Head :title="'فاکتور شماره ' + invoice.id" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    جزئیات فاکتور شماره: {{ invoice.id }}
                </h2>
                <div class="mt-6 flex justify-end print-hidden">
                    <Link :href="route('invoices.print', invoice.id)"
                          class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-md text-sm ml-2">
                        چاپ پیشرفته
                    </Link>

                    <Link :as="isReturnable ? 'a' : 'span'"
                          :href="isReturnable ? route('sales_returns.create_from_invoice', invoice.id) : null"
                          :class="['bg-yellow-500 text-white font-bold py-2 px-4 rounded-md text-sm ml-2 transition-opacity',
                                   { 'opacity-50 cursor-not-allowed': !isReturnable, 'hover:bg-yellow-600': isReturnable }]">
                        برگشت از فروش
                    </Link>

                    <button @click="openPaymentModal"
                            :disabled="!isPayable"
                            :class="['bg-green-500 text-white font-bold py-2 px-4 rounded-md text-sm ml-2 transition-opacity',
                                     { 'opacity-50 cursor-not-allowed': !isPayable, 'hover:bg-green-600': isPayable }]">
                        دریافت وجه
                    </button>

                    <button @click="printInvoice" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                        چاپ ساده
                    </button>
                </div>
            </div>
        </template>

        <ReceivePaymentModal
            :show="isPaymentModalOpen"
            :invoice="invoice"
            :accounts="accounts"
            @close="closePaymentModal"
        />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div id="invoice-content" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-900">
                        <div class="flex justify-between items-start border-b-2 pb-6 mb-6">
                            <div>
                                <h1 class="text-2xl font-bold">{{ companySettings.company_name || 'نام شرکت' }}</h1>
                                <p>{{ companySettings.company_address || 'آدرس شرکت' }}</p>
                                <p>تلفن: {{ companySettings.company_phone || 'تلفن شرکت' }}</p>
                            </div>
                            <div class="text-left">
                                <h2 class="text-xl font-bold">فاکتور فروش</h2>
                                <p>شماره فاکتور: <span class="font-semibold">{{ invoice.id }}</span></p>
                                <p>تاریخ صدور: <span class="font-semibold">{{ formatDate(invoice.issue_date) }}</span></p>
                                <p>تاریخ سررسید: <span class="font-semibold">{{ formatDate(invoice.due_date) }}</span></p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-2">صورتحساب برای:</h3>
                            <p class="font-semibold">{{ invoice.person.name }}</p>
                            <p>{{ invoice.person.address }}</p>
                            <p>تلفن: {{ invoice.person.phone }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                <tr class="text-right">
                                    <th class="py-2 px-4 font-semibold">#</th>
                                    <th class="py-2 px-4 font-semibold">کالا/خدمات</th>
                                    <th class="py-2 px-4 font-semibold">تعداد</th>
                                    <th class="py-2 px-4 font-semibold">قیمت واحد</th>
                                    <th class="py-2 px-4 font-semibold">تخفیف</th>
                                    <th class="py-2 px-4 font-semibold">جمع کل</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in invoice.items" :key="item.id" class="border-b">
                                    <td class="py-3 px-4">{{ index + 1 }}</td>
                                    <td class="py-3 px-4">{{ item.product.name }}</td>
                                    <td class="py-3 px-4">{{ item.quantity }}</td>
                                    <td class="py-3 px-4">{{ formatCurrency(item.unit_price) }}</td>
                                    <td class="py-3 px-4 text-red-600">{{ formatCurrency(item.discount_amount) }}</td>
                                    <td class="py-3 px-4 font-semibold">{{ formatCurrency(item.total_price) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-6">
                            <div class="w-full max-w-sm">
                                <div class="flex justify-between py-2">
                                    <span class="font-semibold">جمع آیتم‌ها:</span>
                                    <span>{{ formatCurrency(invoice.subtotal_amount) }}</span>
                                </div>
                                <div v-if="invoice.discount_amount > 0" class="flex justify-between py-2 text-red-600">
                                    <span class="font-semibold">تخفیف کلی:</span>
                                    <span>({{ formatCurrency(invoice.discount_amount) }})</span>
                                </div>
                                <div class="flex justify-between py-2 border-t-2 font-bold text-lg">
                                    <span class="font-semibold">مبلغ کل:</span>
                                    <span>{{ formatCurrency(invoice.total_amount) }}</span>
                                </div>
                                <div class="flex justify-between py-2 text-green-600">
                                    <span class="font-semibold">پرداخت شده:</span>
                                    <span>{{ formatCurrency(invoice.paid_amount) }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-gray-100 px-4 rounded-md font-bold text-xl">
                                    <span class="font-semibold">مانده:</span>
                                    <span>{{ formatCurrency(invoice.total_amount - invoice.paid_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    .print-hidden {
        display: none;
    }
    body {
        background-color: white;
    }
    #invoice-content {
        box-shadow: none;
        border: none;
        margin: 0;
        padding: 0;
    }
    .py-12 {
        padding: 0;
    }
}
</style>
