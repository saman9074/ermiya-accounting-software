<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { ref, computed, watch } from 'vue';
    import vSelect from "vue-select";
    import "vue-select/dist/vue-select.css";

    const props = defineProps({
    persons: Array,
    products: Array,
});

    const form = useForm({
    person_id: null,
    issue_date: new Date().toISOString().slice(0, 10),
    due_date: null,
    items: [],
    discount_type: 'amount',
    discount_value: 0,
});

    const selectedProduct = ref(null);
    const selectedPerson = ref(null);

    const addItem = () => {
    if (selectedProduct.value) {
    let unitPrice = selectedProduct.value.sale_price; // قیمت پیش‌فرض

    // --- اصلاحیه اصلی: استفاده از group به جای person_group ---
    if (selectedPerson.value && selectedPerson.value.group?.price_list_id && selectedProduct.value.product_prices) {
    const specialPrice = selectedProduct.value.product_prices.find(
    p => p.price_list_id === selectedPerson.value.group.price_list_id
    );
    if (specialPrice) {
    unitPrice = specialPrice.price;
}
}

    form.items.push({
    product_id: selectedProduct.value.id,
    name: selectedProduct.value.name,
    quantity: 1,
    unit_price: unitPrice,
    discount_type: 'amount',
    discount_value: 0,
});
    selectedProduct.value = null;
}
};

    const removeItem = (index) => {
    form.items.splice(index, 1);
};

    watch(() => form.person_id, (newPersonId) => {
    selectedPerson.value = props.persons.find(p => p.id === newPersonId);
    updateAllItemPrices();
});

    const updateAllItemPrices = () => {
    form.items.forEach((item, index) => {
        const product = props.products.find(p => p.id === item.product_id);
        if (product) {
            let newPrice = product.sale_price;

            // --- اصلاحیه اصلی: استفاده از group به جای person_group ---
            if (selectedPerson.value && selectedPerson.value.group?.price_list_id && product.product_prices) {
                const specialPrice = product.product_prices.find(
                    p => p.price_list_id === selectedPerson.value.group.price_list_id
                );
                if (specialPrice) {
                    newPrice = specialPrice.price;
                }
            }
            form.items[index].unit_price = newPrice;
        }
    });
};

    const calculations = computed(() => {
    let subtotal = 0;
    const items = form.items.map(item => {
    const itemSubtotal = item.quantity * item.unit_price;
    let itemDiscountAmount = 0;
    if (item.discount_value > 0) {
    if (item.discount_type === 'percentage') {
    itemDiscountAmount = (itemSubtotal * item.discount_value) / 100;
} else {
    itemDiscountAmount = item.discount_value * item.quantity;
}
}
    const itemTotal = itemSubtotal - itemDiscountAmount;
    subtotal += itemTotal;
    return { ...item, subtotal: itemSubtotal, discount_amount: itemDiscountAmount, total: itemTotal };
});

    let overallDiscountAmount = 0;
    if (form.discount_value > 0) {
    if (form.discount_type === 'percentage') {
    overallDiscountAmount = (subtotal * form.discount_value) / 100;
} else {
    overallDiscountAmount = form.discount_value;
}
}
    const grandTotal = subtotal - overallDiscountAmount;
    return { items, subtotal, overallDiscountAmount, grandTotal };
});

    const submit = () => {
    form.post(route('invoices.store'));
};

    const formatNumber = (number) => {
    if (isNaN(number)) return 0;
    return new Intl.NumberFormat('fa-IR').format(number);
};
</script>

<template>
    <Head title="صدور فاکتور جدید" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">صدور فاکتور جدید</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">مشتری</label>
                                    <v-select dir="rtl" :options="persons" label="name" :reduce="person => person.id" v-model="form.person_id" placeholder="انتخاب کنید..." />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">تاریخ صدور</label>
                                    <input type="date" v-model="form.issue_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">تاریخ سررسید</label>
                                    <input type="date" v-model="form.due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <div class="flex items-end gap-4 border-t pt-4 mb-6">
                                <div class="flex-grow">
                                    <label class="block text-sm font-medium text-gray-700">افزودن کالا</label>
                                    <v-select dir="rtl" :options="products" label="name" v-model="selectedProduct" placeholder="جستجوی کالا..." />
                                </div>
                                <button @click="addItem" type="button" class="bg-green-500 text-white px-4 py-2 rounded-md">افزودن</button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-2 px-4 text-right">کالا</th>
                                        <th class="py-2 px-4 text-right">تعداد</th>
                                        <th class="py-2 px-4 text-right">قیمت واحد</th>
                                        <th class="py-2 px-4 text-right">جمع</th>
                                        <th class="py-2 px-4 text-right" style="width: 200px;">تخفیف</th>
                                        <th class="py-2 px-4 text-right">مبلغ تخفیف</th>
                                        <th class="py-2 px-4 text-right">جمع کل</th>
                                        <th class="py-2 px-4"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in calculations.items" :key="index" class="border-b">
                                        <td class="py-2 px-4">{{ item.name }}</td>
                                        <td class="py-2 px-4"><input type="number" step="any" v-model.number="form.items[index].quantity" class="w-24 text-center border-gray-300 rounded-md shadow-sm"></td>
                                        <td class="py-2 px-4"><input type="number" step="any" v-model.number="form.items[index].unit_price" class="w-32 text-center border-gray-300 rounded-md shadow-sm"></td>
                                        <td class="py-2 px-4">{{ formatNumber(item.subtotal) }}</td>
                                        <td class="py-2 px-4">
                                            <div class="flex items-center">
                                                <input type="number" step="any" v-model.number="form.items[index].discount_value" class="w-24 text-center border-gray-300 rounded-md shadow-sm">
                                                <select v-model="form.items[index].discount_type" class="text-xs border-gray-300 rounded-md shadow-sm ml-1">
                                                    <option value="amount">مبلغ</option>
                                                    <option value="percentage">%</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-red-500">({{ formatNumber(item.discount_amount) }})</td>
                                        <td class="py-2 px-4 font-bold">{{ formatNumber(item.total) }}</td>
                                        <td class="py-2 px-4"><button @click="removeItem(index)" type="button" class="text-red-500 hover:text-red-700">حذف</button></td>
                                    </tr>
                                    <tr v-if="form.items.length === 0">
                                        <td colspan="8" class="text-center py-4">هنوز آیتمی به فاکتور اضافه نشده است.</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 pt-6 border-t grid grid-cols-1 md:grid-cols-2">
                                <div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span>جمع آیتم‌ها:</span>
                                        <span class="font-semibold">{{ formatNumber(calculations.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>تخفیف کلی:</span>
                                        <div class="flex items-center gap-2">
                                            <input type="number" step="any" v-model.number="form.discount_value" class="w-24 text-center border-gray-300 rounded-md shadow-sm">
                                            <select v-model="form.discount_type" class="text-xs border-gray-300 rounded-md shadow-sm">
                                                <option value="amount">مبلغ</option>
                                                <option value="percentage">%</option>
                                            </select>
                                            <span class="text-red-500 w-24 text-center">({{ formatNumber(calculations.overallDiscountAmount) }})</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between font-bold text-xl border-t pt-2">
                                        <span>مبلغ قابل پرداخت:</span>
                                        <span>{{ formatNumber(calculations.grandTotal) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-bold" :disabled="form.processing">ثبت فاکتور</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
