<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCurrency } from '@Core/composables/useCurrency';

const props = defineProps({
    person: Object,
    invoices: Array,
    balance: Number,
    total_invoices: Number,
    total_paid: Number,
    total_returned: Number,
});

const { activeCurrency } = useCurrency();
</script>

<template>
    <Head :title="'صورتحساب ' + person.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                صورت وضعیت حساب: {{ person.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <p><strong>کد:</strong> {{ person.id }}</p>
                            <p><strong>نام:</strong> {{ person.name }}</p>
                            <p><strong>تلفن:</strong> {{ person.phone || '-' }}</p>
                            <p><strong>آدرس:</strong> {{ person.address || '-' }}</p>
                        </div>

                        <h3 class="text-lg font-semibold mb-4 border-t pt-4">لیست فاکتورها</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-right">شماره فاکتور</th>
                                    <th class="py-2 px-4 text-right">تاریخ صدور</th>
                                    <th class="py-2 px-4 text-right">مبلغ کل</th>
                                    <th class="py-2 px-4 text-right">پرداخت شده</th>
                                    <th class="py-2 px-4 text-right text-yellow-600">مبلغ مرجوعی</th>
                                    <th class="py-2 px-4 text-right font-bold">مانده فاکتور</th> <th class="py-2 px-4 text-right">وضعیت</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="invoices.length === 0">
                                    <td colspan="7" class="text-center py-4 text-gray-500">موردی برای نمایش وجود ندارد.</td>
                                </tr>
                                <tr v-for="invoice in invoices" :key="invoice.id" class="border-b hover:bg-gray-50" :class="{'bg-yellow-50 text-gray-600': invoice.status === 'returned'}">

                                    <td class="py-2 px-4">
                                        <Link :href="route('invoices.show', invoice.id)" class="text-blue-600 hover:underline">{{ invoice.id }}</Link>
                                    </td>
                                    <td class="py-2 px-4">{{ new Date(invoice.issue_date).toLocaleDateString('fa-IR') }}</td>
                                    <td class="py-2 px-4" :class="{'line-through': invoice.status === 'returned'}">{{ Number(invoice.total_amount).toLocaleString() }} {{ activeCurrency.display_name }}</td>
                                    <td class="py-2 px-4">{{ Number(invoice.paid_positive_amount).toLocaleString() }} {{ activeCurrency.display_name }}</td>
                                    <td class="py-2 px-4 font-semibold text-yellow-700">{{ Number(invoice.returned_amount).toLocaleString() }} {{ activeCurrency.display_name }}</td>

                                    <td class="py-2 px-4 font-bold" :class="{ 'text-red-600': invoice.invoice_balance > 0, 'text-green-600': invoice.invoice_balance < 0 }">
                                        {{ Number(invoice.invoice_balance).toLocaleString() }} {{ activeCurrency.display_name }}
                                    </td>

                                    <td class="py-2 px-4">
            <span :class="{
                'bg-red-200 text-red-800': invoice.status === 'unpaid',
                'bg-green-200 text-green-800': invoice.status === 'paid',
                'bg-yellow-200 text-yellow-800': invoice.status === 'partially_paid',
                'bg-gray-200 text-gray-800': invoice.status === 'returned',
            }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ invoice.translated_status }}
            </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 text-left border-t pt-4">
                            <p><strong>جمع کل فاکتورها:</strong> {{ Number(total_invoices).toLocaleString() }} {{ activeCurrency.display_name }}</p>
                            <p><strong>جمع کل پرداختی‌ها:</strong> {{ Number(total_paid).toLocaleString() }} {{ activeCurrency.display_name }}</p>
                            <p class="text-yellow-600"><strong>جمع کل برگشتی‌ها:</strong> {{ Number(total_returned).toLocaleString() }} {{ activeCurrency.display_name }}</p>
                            <hr class="my-2">
                            <p class="text-lg font-bold">
                                <strong>مانده نهایی حساب:</strong> {{ Number(Math.abs(balance)).toLocaleString() }} {{ activeCurrency.display_name }}
                                <span v-if="balance < 0" class="text-green-600 font-semibold">(بستانکار)</span>
                                <span v-else-if="balance > 0" class="text-red-600 font-semibold">(بدهکار)</span>
                                <span v-else>(تسویه)</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
