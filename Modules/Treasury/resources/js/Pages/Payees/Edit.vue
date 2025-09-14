<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
const props = defineProps({ payee: Object });
const form = useForm({ name: props.payee.name });
const submit = () => form.put(route('payees.update', props.payee.id));
</script>
<template>
    <Head :title="'ویرایش: ' + payee.name" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl">ویرایش طرف حساب</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white p-8 rounded-lg shadow-sm">
                    <form @submit.prevent="submit">
                        <div>
                            <label for="name">نام طرف حساب</label>
                            <input id="name" type="text" v-model="form.name" class="block w-full mt-1" required>
                            <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4">
                            <Link :href="route('payees.index')">انصراف</Link>
                            <button type="submit" :disabled="form.processing">به‌روزرسانی</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
