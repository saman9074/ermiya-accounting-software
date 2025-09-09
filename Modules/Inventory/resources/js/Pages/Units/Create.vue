<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { watch } from 'vue';

const form = useForm({
    name: '',
    abbreviation: '',
});

// با تغییر نام، مخفف را پیشنهاد بده
watch(() => form.name, (newName) => {
    if (newName) {
        form.abbreviation = newName.split(' ').map(word => word[0]).join('');
    }
});

const submit = () => {
    form.post(route('units.store'));
};
</script>

<template>
    <Head title="افزودن واحد" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">افزودن واحد جدید</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div>
                                <label for="name">نام</label>
                                <input id="name" type="text" v-model="form.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</div>
                            </div>
                            <div class="mt-4">
                                <label for="abbreviation">مخفف</label>
                                <input id="abbreviation" type="text" v-model="form.abbreviation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <div v-if="form.errors.abbreviation" class="text-red-600 text-sm">{{ form.errors.abbreviation }}</div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('units.index')" class="underline text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-gray-800 text-white rounded-md">ذخیره</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
