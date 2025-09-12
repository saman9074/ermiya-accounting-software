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
    stock: props.product.stock, // <-- Add this
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
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">ویرایش کالا: {{ product.name }}</h1>
        </div>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="card">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <!-- Column 1 -->
                    <div>
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">نام کالا *</label>
                            <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <!-- Stock -->
                        <div class="mt-4">
                            <label for="stock" class="block font-medium text-sm text-gray-700">موجودی فعلی</label>
                            <input v-model="form.stock" id="stock" type="number" step="any" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <div v-if="form.errors.stock" class="text-red-600 text-sm mt-1">{{ form.errors.stock }}</div>
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
                            <div v-if="form.errors.sku" class="text-red-600 text-sm mt-1">{{ form.errors.sku }}</div>
                        </div>

                        <!-- Empty space for alignment -->
                        <div class="mt-4">&nbsp;</div>

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
                </div>
                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-3 flex items-center justify-end">
                    <Link :href="route('products.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">به‌روزرسانی</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

