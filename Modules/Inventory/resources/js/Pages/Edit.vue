<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    product: Object,
    categories: Array,
    units: Array,
    priceLists: Array,
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    purchase_price: props.product.purchase_price,
    sale_price: props.product.sale_price,
    stock: props.product.stock,
    unit_id: props.product.unit_id,
    category_id: props.product.category_id,
    prices: [],
});

// Populate the prices array with existing values or defaults
onMounted(() => {
    form.prices = props.priceLists.map(pl => {
        const existingPrice = props.product.product_prices.find(pp => pp.price_list_id === pl.id);
        return {
            price_list_id: pl.id,
            name: pl.name,
            price: existingPrice ? existingPrice.price : 0,
        };
    });
});

const submit = () => {
    form.put(route('products.update', props.product.id));
};
</script>

<template>
    <Head title="ویرایش کالا" />

    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">ویرایش کالا: {{ form.name }}</h1>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Column -->
                <div class="lg:col-span-2">
                    <div class="card p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">نام کالا *</label>
                                <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label for="sku" class="block font-medium text-sm text-gray-700">کد کالا (SKU)</label>
                                <input v-model="form.sku" id="sku" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.sku" class="text-red-600 text-sm mt-1">{{ form.errors.sku }}</div>
                            </div>
                            <div>
                                <label for="unit_id" class="block font-medium text-sm text-gray-700">واحد شمارش *</label>
                                <select v-model="form.unit_id" id="unit_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                                </select>
                                <div v-if="form.errors.unit_id" class="text-red-600 text-sm mt-1">{{ form.errors.unit_id }}</div>
                            </div>
                            <div>
                                <label for="category_id" class="block font-medium text-sm text-gray-700">دسته‌بندی</label>
                                <select v-model="form.category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option :value="null">بدون دسته‌بندی</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="stock" class="block font-medium text-sm text-gray-700">موجودی فعلی *</label>
                                <input v-model="form.stock" id="stock" type="number" step="any" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.stock" class="text-red-600 text-sm mt-1">{{ form.errors.stock }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Column -->
                <div>
                    <div class="card p-6 space-y-4">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">قیمت‌گذاری</h2>
                        <div>
                            <label for="purchase_price" class="block font-medium text-sm text-gray-700">قیمت خرید</label>
                            <input v-model="form.purchase_price" id="purchase_price" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="sale_price" class="block font-medium text-sm text-gray-700">قیمت فروش اصلی *</label>
                            <input v-model="form.sale_price" id="sale_price" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <div v-if="form.errors.sale_price" class="text-red-600 text-sm mt-1">{{ form.errors.sale_price }}</div>
                        </div>

                        <div v-if="priceLists.length > 0" class="border-t pt-4 mt-4">
                            <h3 class="text-base font-medium text-gray-800 mb-2">سطوح قیمت دیگر</h3>
                            <div v-for="(price, index) in form.prices" :key="price.price_list_id" class="mt-2">
                                <label :for="'price_' + price.price_list_id" class="block font-medium text-sm text-gray-700">{{ price.name }}</label>
                                <input v-model="form.prices[index].price" :id="'price_' + price.price_list_id" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <Link :href="route('products.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره تغییرات</button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

