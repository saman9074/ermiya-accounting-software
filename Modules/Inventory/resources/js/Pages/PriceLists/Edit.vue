<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    priceList: Object,
});

const form = useForm({
    name: props.priceList.name,
});

const submit = () => {
    form.put(route('price-lists.update', props.priceList.id));
};
</script>

<template>
    <Head :title="'ویرایش: ' + priceList.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ویرایش سطح قیمت</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">نام سطح قیمت</label>
                                <input id="name" type="text" v-model="form.name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-4">
                                <Link :href="route('price-lists.index')" class="text-sm text-gray-600 hover:underline">انصراف</Link>
                                <button type="submit" :disabled="form.processing" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">
                                    به‌روزرسانی
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
