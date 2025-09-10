<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    financialYears: Array,
    success: String,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('fa-IR');
};

const activateYear = (id) => {
    router.patch(route('financial-years.activate', id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="مدیریت سال مالی" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">مدیریت سال مالی</h1>
            <Link :href="route('financial-years.create')" class="btn-primary">ایجاد سال مالی جدید</Link>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ شروع</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ پایان</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">وضعیت</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="fy in financialYears" :key="fy.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ fy.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(fy.start_date) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ formatDate(fy.end_date) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-center">
                        <span v-if="fy.is_active" class="badge-success">فعال</span>
                        <button v-else @click="activateYear(fy.id)" class="text-xs text-slate-500 hover:text-slate-700">فعال‌سازی</button>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('financial-years.edit', fy.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
