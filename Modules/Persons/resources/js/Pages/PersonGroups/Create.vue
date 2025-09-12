<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    priceLists: Array,
});

const form = useForm({
    name: '',
    price_list_id: null,
});

const submit = () => form.post(route('person-groups.store'));
</script>

<template>
    <Head title="ایجاد گروه جدید" />
    <AuthenticatedLayout>
        <div class="mb-6"><h1 class="text-xl font-bold text-slate-800">ایجاد گروه جدید</h1></div>
        <div class="card max-w-2xl">
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">نام گروه (مثال: همکار)</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                    <label for="price_list_id" class="block font-medium text-sm text-gray-700">سطح قیمت پیش‌فرض (اختیاری)</label>
                    <select v-model="form.price_list_id" id="price_list_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option :value="null">-- بدون سطح قیمت --</option>
                        <option v-for="pl in priceLists" :key="pl.id" :value="pl.id">{{ pl.name }}</option>
                    </select>
                </div>
                <div class="flex items-center justify-end pt-4 border-t mt-6">
                    <Link :href="route('person-groups.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
