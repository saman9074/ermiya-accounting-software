<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import Pagination from '@Core/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    personGroups: Object,
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('person-groups.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteGroup = (id) => {
    if (confirm('آیا از حذف این گروه مطمئن هستید؟')) {
        router.delete(route('person-groups.destroy', id));
    }
};
</script>

<template>
    <Head title="گروه‌های اشخاص" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت گروه‌های اشخاص</h2>
                <Link :href="route('person-groups.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    گروه جدید
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو..."
                                   class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right">نام گروه</th>
                                <th class="px-6 py-3 text-right">سطح قیمت مرتبط</th>
                                <th class="px-6 py-3 text-left">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="group in personGroups.data" :key="group.id">
                                <td class="px-6 py-4">{{ group.name }}</td>
                                <td class="px-6 py-4">{{ group.price_list?.name || '---' }}</td>
                                <td class="px-6 py-4 text-left text-sm font-medium">
                                    <Link :href="route('person-groups.edit', group.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                    <button @click="deleteGroup(group.id)" class="text-red-600 hover:text-red-900 mr-4">حذف</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <Pagination :links="personGroups.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
