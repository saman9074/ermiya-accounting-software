<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    salesReturns: Object,
    filters: Object,
    success: String,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('sales_returns.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('fa-IR').format(number);
};

const deleteReturn = (id) => {
    if (confirm('آیا از حذف این سند اطمینان دارید؟ تمام عملیات مرتبط معکوس خواهد شد.')) {
        router.delete(route('sales_returns.destroy', id));
    }
};
</script>

<template>
    <Head title="اسناد برگشت از فروش" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست اسناد برگشت از فروش</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
                    {{ success }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو بر اساس شماره سند یا نام مشتری..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right">شماره سند</th>
                                <th class="px-6 py-3 text-right">تاریخ</th>
                                <th class="px-6 py-3 text-right">مشتری</th>
                                <th class="px-6 py-3 text-right">فاکتور مرجع</th>
                                <th class="px-6 py-3 text-right">مبلغ کل</th>
                                <th class="px-6 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="sr in salesReturns.data" :key="sr.id">
                                <td class="px-6 py-4">{{ sr.id }}</td>
                                <td class="px-6 py-4">{{ formatDate(sr.return_date) }}</td>
                                <td class="px-6 py-4">{{ sr.person.name }}</td>
                                <td class="px-6 py-4">
                                    <Link :href="route('invoices.show', sr.invoice_id)" class="text-blue-600 hover:underline">
                                        {{ sr.invoice_id }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">{{ formatNumber(sr.total_amount) }}</td>
                                <td class="px-6 py-4 text-left">
                                    <button @click="deleteReturn(sr.id)" class="text-red-600 hover:text-red-900">حذف</button>
                                </td>
                            </tr>
                            <tr v-if="!salesReturns.data.length">
                                <td class="px-6 py-4 text-center" colspan="6">هیچ سندی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>

                        <Pagination :links="salesReturns.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
