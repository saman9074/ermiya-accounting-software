<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCurrency } from '@Core/composables/useCurrency';

const { formatCurrency } = useCurrency();
const props = defineProps({
    invoices: Object,
    filters: Object,
    success: String,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('invoices.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const formatNumber = (number) => {
    if (number === null || isNaN(number)) return 0;
    return new Intl.NumberFormat('fa-IR').format(number);
};
</script>

<template>
    <Head title="لیست فاکتورها" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">لیست فاکتورهای فروش</h2>
                <Link :href="route('invoices.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    فاکتور جدید
                </Link>
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
                            <input type="text" v-model="search" placeholder="جستجو بر اساس شماره فاکتور یا نام مشتری..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right">#</th>
                                    <th class="px-6 py-3 text-right">مشتری</th>
                                    <th class="px-6 py-3 text-right">تاریخ</th>
                                    <th class="px-6 py-3 text-right">مبلغ کل</th>
                                    <th class="px-6 py-3 text-right">وضعیت</th>
                                    <th class="px-6 py-3 text-left">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="invoice in invoices.data" :key="invoice.id">
                                    <td class="px-6 py-4">{{ invoice.id }}</td>
                                    <td class="px-6 py-4">{{ invoice.person.name }}</td>
                                    <td class="px-6 py-4">{{ formatDate(invoice.issue_date) }}</td>
                                    <td class="px-6 py-4">{{ formatCurrency(invoice.total_amount) }}</td>
                                    <td class="px-6 py-4">{{ invoice.translated_status }}</td>
                                    <td class="px-6 py-4 text-left">
                                        <Link :href="route('invoices.show', invoice.id)" class="text-indigo-600 hover:text-indigo-900">
                                            مشاهده
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!invoices.data.length">
                                    <td class="px-6 py-4 text-center" colspan="6">هیچ فاکتوری یافت نشد.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="invoices.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
