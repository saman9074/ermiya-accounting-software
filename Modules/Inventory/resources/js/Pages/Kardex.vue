<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    movements: Array,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const movementTypeMap = {
    sale: 'فروش',
    purchase: 'خرید',
    initial_stock: 'موجودی اولیه',
    sale_return: 'برگشت از فروش',
    purchase_return: 'برگشت از خرید',
    adjustment: 'اصلاحات',
};

const getMovementType = (type) => {
    return movementTypeMap[type] || type;
};

const getReferenceLink = (movement) => {
    if (movement.reference_type && movement.reference_type.includes('InvoiceItem')) {
        return route('invoices.show', movement.reference.invoice_id);
    }
    // Add other reference links here for purchase, etc.
    return null;
}
</script>

<template>
    <Head :title="`کاردکس کالای ${product.name}`" />
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-800">کاردکس کالا: {{ product.name }}</h1>
                <p class="text-sm text-slate-500">موجودی فعلی: <span class="font-bold">{{ product.stock }} {{ product.unit?.name }}</span></p>
            </div>
            <Link :href="route('products.index')" class="text-sm text-gray-600 hover:text-gray-900">بازگشت به لیست کالاها</Link>
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نوع عملیات</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تغییر</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">موجودی پس از عملیات</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">سند مرتبط</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="movement in movements" :key="movement.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(movement.created_at) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ getMovementType(movement.type) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-bold" :class="movement.quantity_change > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ movement.quantity_change > 0 ? '+' : '' }}{{ movement.quantity_change }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ movement.stock_after }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link v-if="getReferenceLink(movement)" :href="getReferenceLink(movement)" class="text-indigo-600 hover:text-indigo-900">
                            مشاهده سند
                        </Link>
                    </td>
                </tr>
                <tr v-if="movements.length === 0">
                    <td colspan="5" class="text-center py-4 text-slate-500">هیچ گردشی برای این کالا ثبت نشده است.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
