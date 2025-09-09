<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// استفاده از هوک useForm برای مدیریت فرم
const form = useForm({
    name: '',
    phone: '',
    address: ''
});

// تابع برای ارسال اطلاعات فرم به بک‌اند
const submit = () => {
    form.post(route('persons.store'), {
        onFinish: () => form.reset('name', 'phone', 'address'), // بعد از ارسال موفق، فرم را خالی کن
    });
};
</script>

<template>
    <Head title="افزودن شخص جدید" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">افزودن شخص جدید</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- فیلد نام -->
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">نام</label>
                                <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>

                            <!-- فیلد تلفن -->
                            <div class="mt-4">
                                <label for="phone" class="block font-medium text-sm text-gray-700">تلفن</label>
                                <input v-model="form.phone" id="phone" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                            </div>

                            <!-- فیلد آدرس -->
                            <div class="mt-4">
                                <label for="address" class="block font-medium text-sm text-gray-700">آدرس</label>
                                <textarea v-model="form.address" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('persons.index')" class="text-sm text-gray-600 hover:text-gray-900 underline ml-4">
                                    انصراف
                                </Link>

                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                                    ذخیره
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
