<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ReceivePaymentModal from '../../Components/ReceivePaymentModal.vue';

const props = defineProps({
    invoice: Object,
    accounts: Array,
    success: String,
});

const showPaymentModal = ref(false);

const paymentStatusText = computed(() => {
    const statuses = {
        unpaid: 'پرداخت نشده',
        partial: 'پرداخت جزئی',
        paid: 'پرداخت شده',
    };
    return statuses[props.invoice.payment_status] || 'نامشخص';
});

const paymentStatusClass = computed(() => {
    return {
        'unpaid': 'badge-danger',
        'partial': 'badge-warning',
        'paid': 'badge-success',
    }[props.invoice.payment_status];
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR', { year: 'numeric', month: 'long', day: 'numeric' });
};

const formatCurrency = (amount) => {
    return new Number(amount).toLocaleString('fa-IR');
};

const printInvoice = () => window.print();

</script>

<template>
    <Head :title="`مشاهده فاکتور #${invoice.id}`" />

    <AuthenticatedLayout>
        <div class="mb-4 flex flex-wrap justify-between items-center print:hidden">
            <h1 class="text-xl font-bold text-slate-800">مشاهده فاکتور</h1>
            <div>
                <Link :href="route('invoices.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">بازگشت</Link>
                <button v-if="invoice.payment_status !== 'paid'" @click="showPaymentModal = true" class="btn-primary ml-2">ثبت دریافت وجه</button>
                <button @click="printInvoice" class="inline-flex items-center justify-center rounded-md border bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50">چاپ</button>
            </div>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700 print:hidden">
            {{ success }}
        </div>

        <div id="invoice-content" class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-start border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold">فاکتور فروش</h2>
                    <p class="text-gray-500">شماره: <span class="font-mono">INV-{{ invoice.id }}</span></p>
                    <div class="mt-2">
                        <span class="font-semibold">وضعیت پرداخت:</span>
                        <span :class="['font-bold', paymentStatusClass]">{{ paymentStatusText }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold">شرکت شما</p>
                </div>
            </div>

            <!-- Details -->
            <div class="flex justify-between mt-6">
                <div>
                    <p class="font-semibold text-gray-700">صورتحساب برای:</p>
                    <p class="font-bold">{{ invoice.person.name }}</p>
                </div>
                <div class="text-right">
                    <p><span class="font-semibold">تاریخ صدور:</span> {{ formatDate(invoice.invoice_date) }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mt-8">
                <table class="w-full text-right">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 text-sm font-semibold text-gray-600">شرح کالا</th>
                        <th class="p-2 text-sm font-semibold text-gray-600 text-center">تعداد</th>
                        <th class="p-2 text-sm font-semibold text-gray-600">قیمت واحد</th>
                        <th class="p-2 text-sm font-semibold text-gray-600 text-left">مبلغ کل</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in invoice.items" :key="item.id" class="border-b">
                        <td class="p-2">{{ item.product.name }}</td>
                        <td class="p-2 text-center">{{ item.quantity }}</td>
                        <td class="p-2">{{ formatCurrency(item.unit_price) }}</td>
                        <td class="p-2 text-left">{{ formatCurrency(item.total_price) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="flex justify-end mt-6">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span>جمع کل:</span>
                        <span>{{ formatCurrency(invoice.total_amount) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2 border-b text-green-600">
                        <span>پرداخت شده:</span>
                        <span>{{ formatCurrency(invoice.paid_amount) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2 bg-gray-100 rounded-b-lg px-2 mt-2">
                        <span class="font-bold text-lg">مبلغ باقیمانده:</span>
                        <span class="font-bold text-lg">{{ formatCurrency(invoice.total_amount - invoice.paid_amount) }} تومان</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ReceivePaymentModal
        :show="showPaymentModal"
        :invoice="invoice"
        :accounts="accounts"
        @close="showPaymentModal = false"
    />
</template>

<style>
@media print {
    body * { visibility: hidden; }
    #invoice-content, #invoice-content * { visibility: visible; }
    #invoice-content {
        position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 1rem;
        border: none; box-shadow: none; border-radius: 0;
    }
}
</style>

