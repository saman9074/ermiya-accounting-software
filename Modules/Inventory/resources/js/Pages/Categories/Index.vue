<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    categories: Array, // حالا یک آرایه درختی است، نه آبجکت صفحه‌بندی
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('categories.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// تابع برای مسطح کردن ساختار درختی و اضافه کردن سطح (level)
const flattenedCategories = computed(() => {
    const flatten = (categories, level = 0, parent = null) => {
        let result = [];
        for (const category of categories) {
            result.push({ ...category, level, parent_name: parent ? parent.name : '---' });
            if (category.children_recursive && category.children_recursive.length > 0) {
                result = result.concat(flatten(category.children_recursive, level + 1, category));
            }
        }
        return result;
    };
    return flatten(props.categories);
});

const deleteCategory = (id) => {
    if (confirm('آیا از حذف این دسته بندی مطمئن هستید؟')) {
        router.delete(route('categories.destroy', id));
    }
};
</script>

<template>
    <Head title="دسته‌بندی‌ها" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مدیریت دسته‌بندی‌ها</h2>
                <Link :href="route('categories.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md text-sm">
                    دسته‌بندی جدید
                </Link>
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="جستجو..." class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right">نام</th>
                                    <th class="px-6 py-3 text-right">دسته‌بندی والد</th>
                                    <th class="px-6 py-3 text-left">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="category in flattenedCategories" :key="category.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" :style="{ 'padding-right': `${category.level * 1.5}rem` }">
                                            <span v-if="category.level > 0" class="text-gray-400 mr-2">└─</span>
                                            {{ category.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.parent_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <Link :href="route('categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900">ویرایش</Link>
                                        <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 mr-4">حذف</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
