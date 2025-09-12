<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    personGroups: Array,
    success: String,
});

const deleteGroup = (id) => {
    if (confirm('آیا از حذف این گروه مطمئن هستید؟')) {
        router.delete(route('person-groups.destroy', id));
    }
};
</script>

<template>
    <Head title="گروه‌های اشخاص" />
    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-800">مدیریت گروه‌های اشخاص</h1>
            <Link :href="route('person-groups.create')" class="btn-primary">ایجاد گروه جدید</Link>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام گروه</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">سطح قیمت پیش‌فرض</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="group in personGroups" :key="group.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ group.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">{{ group.price_list?.name || '---' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('person-groups.edit', group.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                        <button @click="deleteGroup(group.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
