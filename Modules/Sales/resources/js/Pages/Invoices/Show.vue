<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ReceivePaymentModal from '@Sales/Components/ReceivePaymentModal.vue';

const props = defineProps({
    invoice: Object,
    accounts: Array,
    success: String,
});

// Access global settings from page props
const settings = computed(() => usePage().props.settings);
const companyLogoUrl = computed(() => settings.value.company_logo_path ? `/storage/${settings.value.company_logo_path}` : null);


const showPaymentModal = ref(false);

function printInvoice() {
    window.print();
}
</script>

<template>
    <Head :title="`فاکتور شماره ${invoice.invoice_number}`" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between print:hidden">
            <h1 class="text-xl font-bold text-slate-800">مشاهده فاکتور</h1>
            <div class="flex items-center gap-2">
                <button @click="showPaymentModal = true" class="btn-primary">ثبت دریافت وجه</button>
                <Link :href="route('sales_returns.create_from_invoice', invoice.id)"
                      class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                    برگشت از فروش
                </Link>
                <button @click="printInvoice" class="inline-flex items-center justify-center rounded-md border bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">چاپ فاکتور</button>
                <Link :href="route('invoices.index')" class="text-sm text-gray-600 hover:text-gray-900">بازگشت</Link>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700 print:hidden">
            {{ success }}
        </div>

        <!-- Invoice Paper -->
        <div class="bg-white p-8 shadow-lg print:shadow-none" id="invoice-content">
            <!-- Invoice Header -->
            <header class="flex items-center justify-between border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold">{{ settings.company_name || 'نام شرکت' }}</h2>
                    <p class="text-sm text-gray-500">{{ settings.company_address || 'آدرس شرکت' }}</p>
                    <p class="text-sm text-gray-500">تلفن: {{ settings.company_phone || '-' }}</p>
                </div>
                <div class="w-24">
                    <img v-if="companyLogoUrl" :src="companyLogoUrl" alt="Company Logo" class="h-auto w-full">
                </div>
            </header>

            <!-- Invoice Details -->
            <section class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold">فاکتور برای:</h3>
                    <p>{{ invoice.person.name }}</p>
                    <p v-if="invoice.person.address" class="text-gray-500">{{ invoice.person.address }}</p>
                    <p v-if="invoice.person.phone" class="text-gray-500">تلفن: {{ invoice.person.phone }}</p>
                </div>
                <div class="text-left">
                    <h3 class="font-semibold">شماره فاکتور: {{ invoice.invoice_number }}</h3>
                    <p class="text-gray-500">تاریخ صدور: {{ invoice.issue_date }}</p>
                    <p class="text-gray-500">تاریخ سررسید: {{ invoice.due_date || '-' }}</p>
                    <div class="mt-2">
                        <span v-if="invoice.payment_status === 'paid'" class="badge-success">پرداخت شده</span>
                        <span v-else-if="invoice.payment_status === 'partial'" class="badge-warning">پرداخت جزئی</span>
                        <span v-else class="badge-danger">پرداخت نشده</span>
                    </div>
                </div>
            </section>

            <!-- Invoice Items Table -->
            <section class="mt-8">
                <table class="min-w-full">
                    <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-right text-sm font-medium text-slate-600">#</th>
                        <th class="px-4 py-2 text-right text-sm font-medium text-slate-600">کالا/خدمات</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-slate-600">تعداد</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-slate-600">قیمت واحد</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-slate-600">جمع کل</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in invoice.items" :key="item.id" class="border-b">
                        <td class="px-4 py-3">{{ index + 1 }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ item.product.name }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ item.quantity }}</td>
                        <td class="px-4 py-3 text-left">{{ new Intl.NumberFormat('fa-IR').format(item.unit_price) }}</td>
                        <td class="px-4 py-3 text-left font-medium">{{ new Intl.NumberFormat('fa-IR').format(item.total_price) }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <!-- Invoice Footer -->
            <footer class="mt-8 flex justify-end">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">جمع جزء:</span>
                        <span>{{ new Intl.NumberFormat('fa-IR').format(invoice.total_amount) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">پرداخت شده:</span>
                        <span>{{ new Intl.NumberFormat('fa-IR').format(invoice.paid_amount) }} تومان</span>
                    </div>
                    <div class="flex justify-between border-t-2 pt-2 font-bold">
                        <span>مانده حساب:</span>
                        <span>{{ new Intl.NumberFormat('fa-IR').format(invoice.total_amount - invoice.paid_amount) }} تومان</span>
                    </div>
                </div>
            </footer>
        </div>

        <ReceivePaymentModal :show="showPaymentModal" :invoice="invoice" :accounts="accounts" @close="showPaymentModal = false"/>
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
    }
}
</style>

