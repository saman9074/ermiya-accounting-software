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
</script>

<template>
    <Head title="مدیریت کالاها" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت کالاها و خدمات</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <Link :href="route('products.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                افزودن کالای جدید
                            </Link>
                        </div>

                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-right">نام کالا</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-right">قیمت فروش</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-right">واحد</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700">
                            <tr v-for="product in products" :key="product.id" class="border-b">
                                <td class="py-3 px-4 text-right">{{ product.name }}</td>
                                <td class="py-3 px-4 text-right">{{ Number(product.sale_price).toLocaleString() }}</td>
                                <td class="py-3 px-4 text-right">{{ product.unit.name }}</td>
                                <td class="py-3 px-4 text-left">
                                    <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">
                                        ویرایش
                                    </Link>
                                    <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 mx-2">
                                        حذف
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="products.length === 0">
                                <td colspan="4" class="text-center py-4">هیچ کالایی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
