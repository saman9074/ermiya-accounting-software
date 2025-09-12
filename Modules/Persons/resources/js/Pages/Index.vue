<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    persons: Array,
    success: String,
});

const deletePerson = (id) => {
    if (confirm('آیا از حذف این شخص مطمئن هستید؟')) {
        router.delete(route('persons.destroy', id));
    }
};
</script>

<template>
    <Head title="مدیریت اشخاص" />
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-slate-800">مدیریت اشخاص</h1>
            <Link :href="route('persons.create')" class="btn-primary">افزودن شخص جدید</Link>
        </div>

        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">نام</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">گروه</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تلفن</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="person in persons" :key="person.id">
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ person.name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">{{ person.group?.name || '---' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">{{ person.phone }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        <Link :href="route('persons.edit', person.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
                        <button @click="deletePerson(person.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
                    </td>
                </tr>
                <tr v-if="persons.length === 0">
                    <td colspan="4" class="text-center py-4 text-slate-500">هیچ شخصی یافت نشد.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

