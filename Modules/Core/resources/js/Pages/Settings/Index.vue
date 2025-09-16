<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
    success: String,
    currencies: Array,
    active_currency_id: Number,
});

const form = useForm({
    company_name: props.settings.company_name || '',
    company_address: props.settings.company_address || '',
    company_phone: props.settings.company_phone || '',
    company_logo: null,
    default_print_size: props.settings.default_print_size || 'A4',
    active_currency_id: props.active_currency_id,
});

const logoPreview = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.company_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('settings.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="تنظیمات" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">تنظیمات عمومی</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
                    {{ success }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="company_name">نام شرکت</label>
                                <input id="company_name" type="text" v-model="form.company_name" class="mt-1 block w-full">
                            </div>
                            <div>
                                <label for="company_address">آدرس شرکت</label>
                                <textarea id="company_address" v-model="form.company_address" rows="3" class="mt-1 block w-full"></textarea>
                            </div>
                            <div>
                                <label for="company_phone">تلفن شرکت</label>
                                <input id="company_phone" type="text" v-model="form.company_phone" class="mt-1 block w-full">
                            </div>

                            <div>
                                <label for="active_currency_id">واحد پولی نمایش</label>
                                <select id="active_currency_id" v-model="form.active_currency_id" class="mt-1 block w-full">
                                    <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                        {{ currency.display_name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="default_print_size">سایز چاپ پیش‌فرض</label>
                                <select id="default_print_size" v-model="form.default_print_size" class="mt-1 block w-full">
                                    <option value="A4">A4</option>
                                    <option value="A5">A5</option>
                                    <option value="Thermal">حرارتی (Thermal)</option>
                                </select>
                            </div>

                            <div>
                                <label for="company_logo">لوگو شرکت</label>
                                <input id="company_logo" type="file" @change="onFileChange" class="mt-1 block w-full">
                                <div v-if="logoPreview" class="mt-4">
                                    <img :src="logoPreview" class="h-20 rounded-md">
                                </div>
                            </div>

                            <div class="flex justify-end pt-6 border-t">
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-md">
                                    ذخیره تغییرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
