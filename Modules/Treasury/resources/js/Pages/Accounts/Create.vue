<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    type: 'cash', // Default value
    initial_balance: 0,
    description: '',
});

const submit = () => {
    form.post(route('accounts.store'));
};
</script>

<template>
    <Head title="تعریف حساب جدید" />
    <AuthenticatedLayout>
        <h1 class="mb-6 text-xl font-bold text-slate-800">تعریف حساب جدید</h1>

        <div class="card max-w-2xl">
            <form @submit.prevent="submit">
                <!-- Name -->
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">نام حساب</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                </div>

                <!-- Type -->
                <div class="mt-4">
                    <label for="type" class="block font-medium text-sm text-gray-700">نوع حساب</label>
                    <select v-model="form.type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="cash">صندوق</option>
                        <option value="bank">بانک</option>
                    </select>
                    <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</div>
                </div>

                <!-- Initial Balance -->
                <div class="mt-4">
                    <label for="initial_balance" class="block font-medium text-sm text-gray-700">موجودی اولیه</label>
                    <input v-model="form.initial_balance" id="initial_balance" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <div v-if="form.errors.initial_balance" class="text-red-600 text-sm mt-1">{{ form.errors.initial_balance }}</div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <label for="description" class="block font-medium text-sm text-gray-700">توضیحات (اختیاری)</label>
                    <textarea v-model="form.description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <Link :href="route('accounts.index')" class="text-sm text-gray-600 hover:text-gray-900 underline ml-4">
                        انصراف
                    </Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">
                        ذخیره
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
