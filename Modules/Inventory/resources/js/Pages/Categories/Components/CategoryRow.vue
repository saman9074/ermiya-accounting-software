<script setup>
import { Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

// نیازی به تعریف 'const CategoryRow' نیست.
// در <script setup>، کامپوننت به صورت خودکار با نام فایلش قابل استفاده است.

defineProps({
    category: Object,
    level: {
        type: Number,
        default: 0,
    },
});

const deleteCategory = (id) => {
    if (confirm('آیا از حذف این دسته‌بندی و تمام زیرشاخه‌های آن مطمئن هستید؟')) {
        router.delete(route('categories.destroy', id));
    }
};
</script>

<template>
    <!-- سطر اصلی برای دسته‌بندی فعلی -->
    <tr class="border-b">
        <td class="py-3 px-4">
            <span :style="{ marginLeft: level * 20 + 'px' }">
                - {{ category.name }}
            </span>
        </td>
        <td class="py-3 px-4">
            <Link :href="route('categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900 mx-2">ویرایش</Link>
            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 mx-2">حذف</button>
        </td>
    </tr>

    <!-- رندر کردن بازگشتی برای هر فرزند -->
    <!-- از نام صحیح snake_case که از لاراول آمده استفاده می‌کنیم -->
    <CategoryRow
        v-if="category.children_recursive && category.children_recursive.length > 0"
        v-for="child in category.children_recursive"
        :key="child.id"
        :category="child"
        :level="level + 1"
    />
</template>

