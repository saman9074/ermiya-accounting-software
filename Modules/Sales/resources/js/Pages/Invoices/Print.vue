<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    invoice: Object,
    companySettings: Object,
});

const selectedSize = ref(props.companySettings.default_print_size || 'A4');

const print = () => {
    window.print();
};

const formatNumber = (number) => {
    if (number === null || typeof number === 'undefined' || isNaN(number)) {
        return '۰';
    }
    return new Intl.NumberFormat('fa-IR').format(number);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};

const logoUrl = computed(() => {
    return props.companySettings.company_logo ? `/storage/${props.companySettings.company_logo}` : null;
});
</script>

<template>
    <Head :title="`چاپ فاکتور #${invoice.id}`" />

    <div class="print-container">
        <div class="print-hidden sticky top-0 z-50 bg-gray-800 text-white p-3 shadow-lg flex justify-between items-center">
            <div class="flex items-center gap-4">
                <Link :href="route('invoices.show', invoice.id)" class="text-sm hover:underline px-3 py-2 rounded-md hover:bg-gray-700">&larr; بازگشت</Link>
                <div>
                    <label for="print-size" class="text-sm mr-2">سایز چاپ:</label>
                    <select v-model="selectedSize" id="print-size" class="bg-gray-700 border-gray-600 rounded-md text-sm py-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="A4">A4</option>
                        <option value="A5">A5</option>
                        <option value="Thermal">حرارتی (80mm)</option>
                    </select>
                </div>
            </div>
            <button @click="print" class="bg-blue-600 hover:bg-blue-700 font-bold py-2 px-6 rounded-md">چاپ</button>
        </div>

        <main class="py-10">
            <div :class="['invoice-paper', selectedSize.toLowerCase()]">
                <template v-if="selectedSize !== 'Thermal'">
                    <header class="p-8 md:p-10 border-b-4 border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="max-h-20 max-w-40 object-contain">
                                <h1 v-else class="text-3xl font-bold text-gray-800">{{ companySettings.company_name || 'نام شرکت' }}</h1>
                            </div>
                            <div class="text-left">
                                <h2 class="text-xl font-bold text-gray-500 uppercase tracking-widest">فاکتور فروش</h2>
                                <p class="text-sm mt-1">شماره: <span class="font-semibold text-gray-700">{{ invoice.id }}</span></p>
                                <p class="text-sm">تاریخ: <span class="font-semibold text-gray-700">{{ formatDate(invoice.issue_date) }}</span></p>
                            </div>
                        </div>
                    </header>
                    <section class="grid grid-cols-2 gap-8 p-8 md:p-10">
                        <div>
                            <h3 class="font-bold text-sm text-gray-500 mb-2 uppercase">فروشنده</h3>
                            <p class="font-semibold text-gray-800">{{ companySettings.company_name }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ companySettings.company_address }}</p>
                            <p class="text-xs text-gray-600">تلفن: {{ companySettings.company_phone }}</p>
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-sm text-gray-500 mb-2 uppercase">خریدار</h3>
                            <p class="font-semibold text-gray-800">{{ invoice.person.name }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ invoice.person.address }}</p>
                            <p class="text-xs text-gray-600">تلفن: {{ invoice.person.phone }}</p>
                        </div>
                    </section>
                    <section class="p-8 md:p-10">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                            <tr class="text-right text-gray-600">
                                <th class="p-3 font-semibold w-12">#</th>
                                <th class="p-3 font-semibold">شرح کالا / خدمات</th>
                                <th class="p-3 font-semibold text-center">تعداد</th>
                                <th class="p-3 font-semibold text-left">مبلغ واحد</th>
                                <th class="p-3 font-semibold text-left">تخفیف</th>
                                <th class="p-3 font-semibold text-left">مبلغ کل</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in invoice.items" :key="item.id" class="border-b border-gray-100">
                                <td class="p-3 text-gray-500">{{ index + 1 }}</td>
                                <td class="p-3 font-semibold text-gray-800">{{ item.product.name }}</td>
                                <td class="p-3 text-center">{{ item.quantity }}</td>
                                <td class="p-3 text-left">{{ formatNumber(item.unit_price) }}</td>
                                <td class="p-3 text-left text-red-600">({{ formatNumber(item.discount_amount) }})</td>
                                <td class="p-3 text-left font-semibold text-gray-800">{{ formatNumber(item.total_price) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </section>
                    <section class="flex justify-end p-8 md:p-10">
                        <div class="w-full max-w-xs text-left text-sm space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">جمع:</span>
                                <span>{{ formatNumber(invoice.subtotal_amount) }}</span>
                            </div>
                            <div v-if="invoice.discount_amount > 0" class="flex justify-between text-red-600">
                                <span class="text-gray-600">تخفیف کلی:</span>
                                <span>({{ formatNumber(invoice.discount_amount) }})</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                                <span>قابل پرداخت:</span>
                                <span>{{ formatNumber(invoice.total_amount) }}</span>
                            </div>
                        </div>
                    </section>
                </template>

                <template v-else>
                    <div class="p-4">
                        <header class="text-center mb-4">
                            <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="max-h-24 mx-auto mb-2">
                            <h1 class="text-lg font-bold">{{ companySettings.company_name }}</h1>
                            <p class="text-xs">{{ companySettings.company_address }}</p>
                            <p class="text-xs">تلفن: {{ companySettings.company_phone }}</p>
                            <hr class="border-dashed my-2">
                            <p class="text-xs">شماره فاکتور: {{ invoice.id }}</p>
                            <p class="text-xs">تاریخ: {{ formatDate(invoice.issue_date) }}</p>
                            <p class="text-xs">مشتری: {{ invoice.person.name }}</p>
                        </header>
                        <hr class="border-dashed my-2">
                        <section>
                            <div v-for="item in invoice.items" :key="item.id" class="text-xs my-2">
                                <p class="font-semibold">{{ item.product.name }}</p>
                                <div class="flex justify-between">
                                    <span>{{ item.quantity }} x {{ formatNumber(item.unit_price) }}</span>
                                    <span>{{ formatNumber(item.quantity * item.unit_price) }}</span>
                                </div>
                                <div v-if="item.discount_amount > 0" class="flex justify-between text-red-600">
                                    <span>تخفیف:</span>
                                    <span>({{ formatNumber(item.discount_amount) }})</span>
                                </div>
                            </div>
                        </section>
                        <hr class="border-dashed my-2">
                        <section class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span>جمع:</span>
                                <span>{{ formatNumber(invoice.subtotal_amount) }}</span>
                            </div>
                            <div v-if="invoice.discount_amount > 0" class="flex justify-between text-red-600">
                                <span>تخفیف کلی:</span>
                                <span>({{ formatNumber(invoice.discount_amount) }})</span>
                            </div>
                            <div class="flex justify-between font-bold text-base pt-1 mt-1 border-t border-dashed">
                                <span>قابل پرداخت:</span>
                                <span>{{ formatNumber(invoice.total_amount) }}</span>
                            </div>
                        </section>
                        <footer class="text-center text-xs mt-4">
                            <p>با تشکر از خرید شما</p>
                            <p>نرم افزار حسابداری ارمیا</p>
                        </footer>
                    </div>
                </template>

                <div v-if="selectedSize !== 'Thermal'" class="invoice-footer">
                    <p>با تشکر از حسن انتخاب شما - نرم افزار حسابداری ارمیا</p>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
/* Base styles */
.font-vazir { font-family: 'Vazirmatn', sans-serif; }
.print-container { background-color: #e5e7eb; font-family: 'Vazirmatn', sans-serif; }

/* Styles for paper effect */
.invoice-paper {
    background-color: white;
    margin: 0 auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    position: relative;
    padding-bottom: 40px; /* Space for footer */
}
.invoice-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    padding: 10px;
    font-size: 0.75rem;
    color: #9ca3af;
    border-top: 1px solid #f3f4f6;
}

/* Page size styles */
.a4 { width: 210mm; min-height: 297mm; }
.a5 { width: 148mm; min-height: 210mm; font-size: 0.85rem; }
.thermal { width: 78mm; padding-bottom: 10px; }

/* Print media queries */
@media print {
    .print-hidden { display: none !important; }
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: white !important;
    }
    .print-container { background-color: white !important; }
    main { padding: 0 !important; }
    .invoice-paper {
        box-shadow: none !important;
        margin: 0 !important;
        width: 100% !important;
        min-height: auto !important;
    }
    @page {
        margin: 0;
    }
    .a4 { size: A4; }
    .a5 { size: A5; }
    .thermal { size: 80mm auto; }
}
</style>
