<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    persons: Array,
    products: Array,
});

// Ref for the product selected in the dropdown
const productToAdd = ref(null);

const form = useForm({
    person_id: null,
    invoice_date: new Date().toISOString().slice(0, 10), // Default to today
    items: [],
});

// Computed property to calculate the total amount of the invoice
const totalAmount = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (item.quantity * item.unit_price);
    }, 0);
});

// Function to add a selected product to the invoice items
const addProduct = () => {
    if (!productToAdd.value) return;

    // Check if product is already in the list
    const existingItem = form.items.find(item => item.product_id === productToAdd.value.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        form.items.push({
            product_id: productToAdd.value.id,
            name: productToAdd.value.name,
            quantity: 1,
            unit_price: productToAdd.value.sale_price,
        });
    }
    // Reset dropdown
    productToAdd.value = null;
};

// Function to remove an item from the invoice
const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('invoices.store'));
};
</script>

<template>
    <Head title="صدور فاکتور جدید" />
    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">صدور فاکتور جدید</h1>
        </div>

        <form @submit.prevent="submit">
            <div class="card">
                <!-- Invoice Header -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="person_id" class="block text-sm font-medium text-gray-700">مشتری</label>
                        <select v-model="form.person_id" id="person_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option :value="null" disabled>یک مشتری انتخاب کنید</option>
                            <option v-for="person in persons" :key="person.id" :value="person.id">
                                {{ person.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.person_id" class="mt-1 text-sm text-red-600">{{ form.errors.person_id }}</div>
                    </div>
                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700">تاریخ فاکتور</label>
                        <input v-model="form.invoice_date" type="date" id="invoice_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        <div v-if="form.errors.invoice_date" class="mt-1 text-sm text-red-600">{{ form.errors.invoice_date }}</div>
                    </div>
                </div>

                <!-- Add Product Section -->
                <div class="mt-8 border-t pt-6">
                    <label for="product_add" class="block text-sm font-medium text-gray-700 mb-2">افزودن کالا به فاکتور</label>
                    <div class="flex items-center gap-2">
                        <select v-model="productToAdd" id="product_add" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option :value="null" disabled>یک کالا انتخاب کنید</option>
                            <option v-for="product in products" :key="product.id" :value="product">
                                {{ product.name }} (قیمت: {{ product.sale_price }})
                            </option>
                        </select>
                        <button @click.prevent="addProduct" type="button" class="btn-primary whitespace-nowrap">افزودن</button>
                    </div>
                </div>

                <!-- Invoice Items Table -->
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">کالا</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تعداد</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">قیمت واحد</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">جمع کل</th>
                                <th class="w-10"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="(item, index) in form.items" :key="item.product_id">
                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ item.name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <input v-model.number="item.quantity" type="number" class="w-20 rounded-md border-gray-300 text-sm"/>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <input v-model.number="item.unit_price" type="number" class="w-32 rounded-md border-gray-300 text-sm"/>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    {{ (item.quantity * item.unit_price).toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <button @click.prevent="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                </td>
                            </tr>
                            <tr v-if="form.items.length === 0">
                                <td colspan="5" class="text-center py-4 text-sm text-gray-500">هیچ کالایی به فاکتور اضافه نشده است.</td>
                            </tr>
                            </tbody>
                            <tfoot v-if="form.items.length > 0">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-left text-sm font-bold text-gray-700">جمع کل فاکتور:</td>
                                <td colspan="2" class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ totalAmount.toLocaleString() }} تومان</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div v-if="form.errors.items" class="mt-2 text-sm text-red-600">{{ form.errors.items }}</div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                    <Link :href="route('invoices.index')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">انصراف</Link>
                    <button type="submit" :disabled="form.processing" class="btn-primary">ثبت فاکتور</button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
