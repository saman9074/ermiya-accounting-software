<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({ name: '' });
const submit = () => form.post(route('price-lists.store'));
</script>

<template>
    <Head title="ایجاد سطح قیمت" />
    <AuthenticatedLayout>
        <div class="mb-6"><h1 class="text-xl font-bold text-slate-800">ایجاد سطح قیمت جدید</h1></div>
        <div class="card max-w-2xl">
            <form @submit.prevent="submit">
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">نام (مثال: قیمت همکار)</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <Link :href="route('price-lists.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
