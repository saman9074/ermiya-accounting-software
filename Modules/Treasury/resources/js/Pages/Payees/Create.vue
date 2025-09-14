<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
const form = useForm({ name: '' });
const submit = () => form.post(route('payees.store'));
</script>
<template>
    <Head title="ایجاد طرف حساب" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ایجاد طرف حساب جدید</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <form @submit.prevent="submit">
                            <div>
                                <label for="name">نام طرف حساب</label>
                                <input id="name" type="text" v-model="form.name" class="block w-full mt-1" required>
                                <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div class="mt-6 flex justify-end gap-4">
                                <Link :href="route('payees.index')">انصراف</Link>
                                <button type="submit" :disabled="form.processing">ذخیره</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
