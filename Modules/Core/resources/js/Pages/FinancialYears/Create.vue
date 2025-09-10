<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    is_active: false,
});

const submit = () => {
    form.post(route('financial-years.store'));
};
</script>

<template>
    <Head title="ایجاد سال مالی جدید" />
    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">ایجاد سال مالی جدید</h1>
        </div>

        <div class="max-w-2xl">
            <div class="card">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">نام سال مالی (مثلا: سال ۱۴۰۳)</label>
                        <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label for="start_date" class="block font-medium text-sm text-gray-700">تاریخ شروع</label>
                        <input v-model="form.start_date" id="start_date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.start_date" class="text-red-600 text-sm mt-1">{{ form.errors.start_date }}</div>
                    </div>
                    <div>
                        <label for="end_date" class="block font-medium text-sm text-gray-700">تاریخ پایان</label>
                        <input v-model="form.end_date" id="end_date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <div v-if="form.errors.end_date" class="text-red-600 text-sm mt-1">{{ form.errors.end_date }}</div>
                    </div>
                    <div class="flex items-center">
                        <input v-model="form.is_active" id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">فعال کردن این سال مالی؟ (سایر سال‌ها غیرفعال می‌شوند)</label>
                    </div>
                    <div class="flex items-center justify-end pt-4 border-t mt-6">
                        <Link :href="route('financial-years.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
