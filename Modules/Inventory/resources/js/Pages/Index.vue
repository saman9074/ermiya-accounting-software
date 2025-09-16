<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCurrency } from '@Core/composables/useCurrency';

const { formatCurrency } = useCurrency();

const props = defineProps({
    products: Object,
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('products.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const formatNumber = (number) => {
    return new Intl.NumberFormat('fa-IR').format(number);
};

const deleteProduct = (id) => {
    if (confirm('آیا از حذف این کالا مطمئن هستید؟')) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="کالاها" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت کالاها</h2>
                <Link :href="route('products.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    کالای جدید
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو بر اساس نام یا کد کالا..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right">نام کالا</th>
                                    <th class="px-6 py-3 text-right">قیمت فروش</th>
                                    <th class="px-6 py-3 text-right">موجودی</th>
                                    <th class="px-6 py-3 text-right">واحد</th>
                                    <th class="px-6 py-3 text-left">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="product in products.data" :key="product.id">
                                    <td class="px-6 py-4">{{ product.name }}</td>
                                    <td class="px-6 py-4">{{ formatCurrency(product.sale_price) }}</td>
                                    <td class="px-6 py-4">{{ product.stock }}</td>
                                    <td class="px-6 py-4">{{ product.unit.name }}</td>
                                    <td class="px-6 py-4 text-left text-sm font-medium">
                                        <Link :href="route('products.kardex', product.id)" class="text-gray-600 hover:text-gray-900">کاردکس</Link>
                                        <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 mx-4">ویرایش</Link>
                                        <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">حذف</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="products.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
