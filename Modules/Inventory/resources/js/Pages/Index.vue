<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    products: Array
});

const deleteProduct = (id) => {
    if (confirm('آیا از حذف این کالا مطمئن هستید؟')) {
        router.delete(route('products.destroy', id));
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fa-IR').format(value || 0);
};

</script>

<template>
    <Head title="مدیریت کالاها" />

    <AuthenticatedLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-slate-800">مدیریت کالاها و خدمات</h1>
            <Link :href="route('products.create')" class="btn-primary">افزودن کالای جدید</Link>
        </div>


        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام کالا</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">قیمت فروش</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">موجودی</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">واحد</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="product in products" :key="product.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ product.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatCurrency(product.sale_price) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm font-bold" :class="{'text-red-600': product.stock <= 0}">{{ product.stock }}</td>
                    <!-- The optional chaining operator (?.) prevents an error if product.unit is null or undefined -->
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ product.unit?.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('products.kardex', product.id)" class="text-gray-600 hover:text-gray-900 mx-2">کاردکس</Link>
                        <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                        <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                    </td>
                </tr>
                <tr v-if="products.length === 0">
                    <td colspan="5" class="text-center py-4">هیچ کالایی یافت نشد.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

