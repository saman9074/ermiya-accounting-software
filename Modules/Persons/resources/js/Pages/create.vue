<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    personGroups: Array,
});

const form = useForm({
    name: '',
    phone: '',
    address: '',
    person_group_id: null,
});

const submit = () => {
    form.post(route('persons.store'));
};
</script>

<template>
    <Head title="افزودن شخص جدید" />
    <AuthenticatedLayout>
        <h1 class="mb-6 text-xl font-bold text-slate-800">افزودن شخص جدید</h1>
        <div class="card max-w-2xl">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">نام *</label>
                        <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label for="person_group_id" class="block font-medium text-sm text-gray-700">گروه</label>
                        <select v-model="form.person_group_id" id="person_group_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option :value="null">-- انتخاب گروه --</option>
                            <option v-for="group in personGroups" :key="group.id" :value="group.id">{{ group.name }}</option>
                        </select>
                        <div v-if="form.errors.person_group_id" class="text-red-600 text-sm mt-1">{{ form.errors.person_group_id }}</div>
                    </div>
                    <div>
                        <label for="phone" class="block font-medium text-sm text-gray-700">تلفن</label>
                        <input v-model="form.phone" id="phone" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block font-medium text-sm text-gray-700">آدرس</label>
                        <textarea v-model="form.address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex items-center justify-end">
                    <Link :href="route('persons.index')" class="text-sm text-gray-600 hover:text-gray-900 ml-4">انصراف</Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

