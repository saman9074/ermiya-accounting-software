<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
    success: String,
});

const form = useForm({
    company_name: props.settings.company_name || '',
    company_address: props.settings.company_address || '',
    company_phone: props.settings.company_phone || '',
    company_logo: null, // For file input
});

// To display the current logo
const logoPreview = ref(props.settings.company_logo_path ? `/storage/${props.settings.company_logo_path}` : null);

function handleLogoChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.company_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    form.post(route('settings.store'), {
        // preserveState: true allows the modal to stay open on validation errors
        // forceFormData: true is necessary for file uploads
    });
}
</script>

<template>
    <Head title="تنظیمات شرکت" />
    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">تنظیمات فروشگاه</h1>
            <p class="text-sm text-slate-500">اطلاعات کسب‌وکار خود را در اینجا مدیریت کنید.</p>
        </div>

        <!-- Success Message -->
        <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ success }}
        </div>

        <div class="card max-w-2xl">
            <form @submit.prevent="submit">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block font-medium text-sm text-gray-700">نام فروشگاه</label>
                    <input v-model="form.company_name" id="company_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <div v-if="form.errors.company_name" class="text-red-600 text-sm mt-1">{{ form.errors.company_name }}</div>
                </div>

                <!-- Company Phone -->
                <div class="mt-4">
                    <label for="company_phone" class="block font-medium text-sm text-gray-700">تلفن</label>
                    <input v-model="form.company_phone" id="company_phone" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <div v-if="form.errors.company_phone" class="text-red-600 text-sm mt-1">{{ form.errors.company_phone }}</div>
                </div>

                <!-- Company Address -->
                <div class="mt-4">
                    <label for="company_address" class="block font-medium text-sm text-gray-700">آدرس</label>
                    <textarea v-model="form.company_address" id="company_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    <div v-if="form.errors.company_address" class="text-red-600 text-sm mt-1">{{ form.errors.company_address }}</div>
                </div>

                <!-- Company Logo -->
                <div class="mt-4">
                    <label for="company_logo" class="block font-medium text-sm text-gray-700">لوگو</label>
                    <input @change="handleLogoChange" id="company_logo" type="file" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    <div v-if="form.errors.company_logo" class="text-red-600 text-sm mt-1">{{ form.errors.company_logo }}</div>
                    <div v-if="logoPreview" class="mt-4">
                        <img :src="logoPreview" alt="Preview" class="h-20 w-auto rounded-md border p-1">
                    </div>
                </div>


                <div class="flex items-center justify-end mt-6">
                    <button type="submit" :disabled="form.processing" class="btn-primary">
                        ذخیره تغییرات
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

