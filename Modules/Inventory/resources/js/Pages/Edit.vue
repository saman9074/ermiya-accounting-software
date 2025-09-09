<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    categories: Array,
    units: Array,
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    description: props.product.description,
    purchase_price: props.product.purchase_price,
    sale_price: props.product.sale_price,
    category_id: props.product.category_id,
    unit_id: props.product.unit_id,
});

const submit = () => {
    form.put(route('products.update', props.product.id));
};
</script>

<template>
    <Head title="ویرایش کالا" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ویرایش کالا: {{ product.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Column 1 -->
                            <div>
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700">نام کالا *</label>
                                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                                </div>

                                <!-- Purchase Price -->
                                <div class="mt-4">
                                    <label for="purchase_price" class="block font-medium text-sm text-gray-700">قیمت خرید</label>
                                    <input v-model="form.purchase_price" id="purchase_price" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Unit -->
                                <div class="mt-4">
                                    <label for="unit_id" class="block font-medium text-sm text-gray-700">واحد شمارش *</label>
                                    <select v-model="form.unit_id" id="unit_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                                    </select>
                                    <div v-if="form.errors.unit_id" class="text-red-600 text-sm mt-1">{{ form.errors.unit_id }}</div>
                                </div>
                            </div>
                            <!-- Column 2 -->
                            <div>
                                <!-- SKU -->
                                <div>
                                    <label for="sku" class="block font-medium text-sm text-gray-700">کد کالا (SKU)</label>
                                    <input v-model="form.sku" id="sku" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Sale Price -->
                                <div class="mt-4">
                                    <label for="sale_price" class="block font-medium text-sm text-gray-700">قیمت فروش</label>
                                    <input v-model="form.sale_price" id="sale_price" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Category -->
                                <div class="mt-4">
                                    <label for="category_id" class="block font-medium text-sm text-gray-700">دسته‌بندی</label>
                                    <select v-model="form.category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option :value="null">بدون دسته‌بندی</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Description (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="description" class="block font-medium text-sm text-gray-700">توضیحات</label>
                                <textarea v-model="form.description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <!-- Actions (Full Width) -->
                            <div class="md:col-span-2 flex items-center justify-end mt-4">
                                <Link :href="route('products.index')" class="text-sm text-gray-600 hover:text-gray-900 underline ml-4">
                                    انصراف
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                                    به‌روزرسانی
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
