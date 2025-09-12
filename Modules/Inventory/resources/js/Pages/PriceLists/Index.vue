<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    priceLists: Array,
    success: String,
});

const deletePriceList = (id) => {
    if (confirm('آیا از حذف این سطح قیمت مطمئن هستید؟')) {
        router.delete(route('price-lists.destroy', id));
    }
};
</script>

<template>
    <Head title="سطوح قیمت" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">مدیریت سطوح قیمت</h1>
            <Link :href="route('price-lists.create')" class="btn-primary">ایجاد سطح قیمت جدید</Link>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام سطح قیمت</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="pl in priceLists" :key="pl.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ pl.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('price-lists.edit', pl.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                        <button @click="deletePriceList(pl.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                    </td>
                </tr>
                <tr v-if="priceLists.length === 0">
                    <td colspan="2" class="text-center py-4 text-slate-500">هیچ سطح قیمتی یافت نشد.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
