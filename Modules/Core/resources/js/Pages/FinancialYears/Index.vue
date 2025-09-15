<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    financialYears: Object, // از Array به Object تغییر کرد
    filters: Object,
    success: String,
    active_year: Number,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('financial-years.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const activateYear = (id) => {
    if (confirm('آیا مطمئن هستید که می‌خواهید این سال مالی را فعال کنید؟')) {
        router.patch(route('financial-years.activate', id), {}, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="سال‌های مالی" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت سال‌های مالی</h2>
                <Link :href="route('financial-years.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    سال مالی جدید
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
                            <input type="text" v-model="search" placeholder="جستجو..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right">نام</th>
                                    <th class="px-6 py-3 text-right">تاریخ شروع</th>
                                    <th class="px-6 py-3 text-right">تاریخ پایان</th>
                                    <th class="px-6 py-3 text-right">وضعیت</th>
                                    <th class="px-6 py-3 text-left">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="year in financialYears.data" :key="year.id">
                                    <td class="px-6 py-4">{{ year.name }}</td>
                                    <td class="px-6 py-4">{{ year.start_date_jalali }}</td>
                                    <td class="px-6 py-4">{{ year.end_date_jalali }}</td>
                                    <td class="px-6 py-4">
                                            <span v-if="year.id === active_year" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                فعال
                                            </span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                غیرفعال
                                            </span>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <button v-if="year.id !== active_year" @click="activateYear(year.id)" class="text-green-600 hover:text-green-900 ml-4">فعال‌سازی</button>
                                        <Link :href="route('financial-years.edit', year.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="financialYears.links" class="mt-6" />

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
