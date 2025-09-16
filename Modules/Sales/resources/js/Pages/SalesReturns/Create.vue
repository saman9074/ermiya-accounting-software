<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import JalaliDatePicker from '@Core/Components/JalaliDatePicker.vue';
import { useCurrency } from '@Core/composables/useCurrency';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    invoice: Object,
});

// ۱. ایمپورت کامل توابع مورد نیاز
const { activeCurrency, formatNumber, fromBaseCurrency } = useCurrency();

const form = useForm({
    invoice_id: props.invoice.id,
    return_date: new Date().toISOString().slice(0, 10),
    description: '',
    // ۲. قیمت واحد هر آیتم را با تابع fromBaseCurrency تبدیل می‌کنیم
    items: props.invoice.items.map(item => ({
        product_id: item.product_id,
        name: item.product.name,
        original_quantity: item.quantity,
        returnable_quantity: item.returnable_quantity,
        quantity: 0,
        unit_price: fromBaseCurrency(item.unit_price), // <-- اصلاح اصلی اینجا
    })),
});

const totalReturnAmount = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (item.quantity * item.unit_price);
    }, 0);
});

const submit = () => {
    const itemsToSubmit = form.items
        .filter(item => item.quantity > 0)
        .map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            unit_price: item.unit_price, // قیمت به واحد پول نمایشی ارسال می‌شود
        }));

    if (itemsToSubmit.length === 0) {
        alert('لطفا تعداد کالای مرجوعی را برای حداقل یک آیتم مشخص کنید.');
        return;
    }

    form.transform(data => ({
        ...data,
        items: itemsToSubmit,
    })).post(route('sales_returns.store'));
};
</script>

<template>
    <Head :title="'برگشت از فروش فاکتور ' + invoice.id" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ثبت برگشت از فروش برای فاکتور شماره: {{ invoice.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="person_name" class="block text-sm font-medium text-gray-700">مشتری</label>
                                    <input type="text" :value="invoice.person.name" id="person_name" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                                </div>
                                <div>
                                    <label for="return_date" class="block text-sm font-medium text-gray-700">تاریخ برگشت</label>
                                    <JalaliDatePicker id="return_date" v-model="form.return_date" class="block w-full mt-1" required />
                                    <div v-if="form.errors.return_date" class="text-sm text-red-600 mt-1">{{ form.errors.return_date }}</div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h3 class="text-lg font-bold mb-4">آیتم‌های قابل برگشت</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                        <tr>
                                            <th class="text-right py-2 px-2">کالا</th>
                                            <th class="text-right py-2 px-2">قیمت واحد ({{ activeCurrency.display_name }})</th>
                                            <th class="text-right py-2 px-2">فروخته شده</th>
                                            <th class="text-right py-2 px-2">قابل برگشت</th>
                                            <th class="text-right py-2 px-2" width="150px">تعداد مرجوعی</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index" :class="{ 'bg-gray-100': item.returnable_quantity <= 0 }">
                                            <td class="py-2 px-2">{{ item.name }}</td>
                                            <td class="py-2 px-2">{{ formatNumber(item.unit_price) }}</td>
                                            <td class="py-2 px-2">{{ item.original_quantity }}</td>
                                            <td class="py-2 px-2 font-bold">{{ item.returnable_quantity }}</td>
                                            <td class="py-2 px-2">
                                                <input
                                                    type="number"
                                                    v-model="item.quantity"
                                                    class="w-full text-center border-gray-300 rounded-md shadow-sm"
                                                    min="0"
                                                    :max="item.returnable_quantity"
                                                    step="any"
                                                    :disabled="item.returnable_quantity <= 0"
                                                >
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div v-if="form.errors.items" class="text-sm text-red-600 mt-2">{{ form.errors.items }}</div>
                                </div>
                            </div>

                            <div class="mt-6 border-t pt-4">
                                <p class="text-lg font-bold">مبلغ کل برگشتی: {{ formatNumber(totalReturnAmount) }} {{ activeCurrency.display_name }}</p>
                            </div>

                            <div class="mt-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">توضیحات</label>
                                <textarea v-model="form.description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="form.processing">
                                    ثبت برگشت از فروش
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
