<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    persons: Object,
    filters: Object,
    success: String,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('persons.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deletePerson = (id) => {
    if (confirm('آیا از حذف این شخص مطمئن هستید؟')) {
        router.delete(route('persons.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="اشخاص" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت اشخاص</h2>
                <Link :href="route('persons.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    شخص جدید
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="success" class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-700">
                    {{ success }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو بر اساس نام یا تلفن..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right">نام</th>
                                    <th class="px-6 py-3 text-right">گروه</th>
                                    <th class="px-6 py-3 text-right">تلفن</th>
                                    <th class="px-6 py-3 text-left">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="person in persons.data" :key="person.id">
                                    <td class="px-6 py-4">{{ person.name }}</td>
                                    <td class="px-6 py-4">{{ person.group?.name || '---' }}</td>
                                    <td class="px-6 py-4">{{ person.phone }}</td>
                                    <td class="px-6 py-4 text-left text-sm font-medium">
                                        <Link :href="route('persons.statement', person.id)" class="text-blue-600 hover:text-blue-900">صورتحساب</Link>
                                        <Link :href="route('persons.edit', person.id)" class="text-indigo-600 hover:text-indigo-900 mx-4">ویرایش</Link>
                                        <button @click="deletePerson(person.id)" class="text-red-600 hover:text-red-900">حذف</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="persons.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
