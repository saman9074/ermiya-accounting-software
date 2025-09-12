<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array,
    units: Array,
    priceLists: Array,
});

const form = useForm({
    name: '',
    sku: '',
    purchase_price: 0,
    sale_price: 0,
    stock: 0,
    unit_id: null,
    category_id: null,
    prices: [], // Array to hold prices for different price lists
});

// Initialize the prices array based on available price lists
props.priceLists.forEach(pl => {
    form.prices.push({
        price_list_id: pl.id,
        name: pl.name, // For display purposes
        price: 0,
    });
});

const submit = () => {
    form.post(route('products.store'));
};
</script>

<template>
    <Head title="افزودن کالای جدید" />

    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">افزودن کالای جدید</h1>
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
                                    <option :value="null" disabled>یک واحد انتخاب کنید</option>
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
                                <label for="stock" class="block font-medium text-sm text-gray-700">موجودی اولیه *</label>
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
                <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره کالا</button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

