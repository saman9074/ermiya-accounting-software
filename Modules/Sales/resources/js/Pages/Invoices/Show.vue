<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
});

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fa-IR', options);
};

// Helper to format currency
const formatCurrency = (amount) => {
    if (typeof amount !== 'number') return 0;
    return new Number(amount).toLocaleString('fa-IR');
};

const printInvoice = () => {
    window.print();
};
</script>

<template>
    <Head :title="`مشاهده فاکتور #${invoice.id}`" />
    <AuthenticatedLayout>
        <div class="mb-4 flex justify-between items-center print:hidden">
            <h1 class="text-xl font-bold text-slate-800">مشاهده فاکتور</h1>
            <div>
                <Link :href="route('invoices.index')" class="ml-2 text-sm text-gray-600 hover:text-gray-900">
                    بازگشت به لیست
                </Link>
                <button @click="printInvoice" class="btn-primary">
                    چاپ فاکتور
                </button>
            </div>
        </div>

        <div id="invoice-content" class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
            <!-- Invoice Header -->
            <div class="flex justify-between items-center border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold">فاکتور فروش</h2>
                    <p class="text-gray-500">شماره: <span class="font-mono">INV-{{ invoice.id }}</span></p>
                </div>
                <div class="text-right">
                    <p class="font-semibold">شرکت شما</p>
                    <p class="text-sm text-gray-600">آدرس شما</p>
                    <p class="text-sm text-gray-600">تلفن: 12345678</p>
                </div>
            </div>

            <!-- Customer and Invoice Details -->
            <div class="flex justify-between mt-6">
                <div>
                    <p class="font-semibold text-gray-700">صورتحساب برای:</p>
                    <p class="font-bold">{{ invoice.person.name }}</p>
                    <p v-if="invoice.person.address" class="text-sm text-gray-600">{{ invoice.person.address }}</p>
                    <p v-if="invoice.person.phone" class="text-sm text-gray-600">تلفن: {{ invoice.person.phone }}</p>
                </div>
                <div class="text-right">
                    <p><span class="font-semibold">تاریخ صدور:</span> {{ formatDate(invoice.invoice_date) }}</p>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="mt-8">
                <table class="w-full text-right">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-sm font-semibold text-gray-600">شرح کالا/خدمات</th>
                        <th class="p-2 text-sm font-semibold text-gray-600 text-center">تعداد</th>
                        <th class="p-2 text-sm font-semibold text-gray-600">قیمت واحد</th>
                        <th class="p-2 text-sm font-semibold text-gray-600 text-left">مبلغ کل</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in invoice.items" :key="item.id" class="border-b">
                        <td class="p-2">{{ item.product.name }}</td>
                        <td class="p-2 text-center">{{ item.quantity }}</td>
                        <td class="p-2">{{ formatCurrency(Number(item.unit_price)) }}</td>
                        <td class="p-2 text-left">{{ formatCurrency(Number(item.total_price)) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Invoice Total -->
            <div class="flex justify-end mt-6">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-semibold">جمع کل:</span>
                        <span>{{ formatCurrency(Number(invoice.total_amount)) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2 bg-gray-100 rounded-b-lg px-2 mt-2">
                        <span class="font-bold text-lg">مبلغ قابل پرداخت:</span>
                        <span class="font-bold text-lg">{{ formatCurrency(Number(invoice.total_amount)) }} تومان</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center text-xs text-gray-500">
                <p>از خرید شما سپاسگزاریم!</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-content, #invoice-content * {
        visibility: visible;
    }
    #invoice-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
        border-radius: 0;
    }
}
</style>
