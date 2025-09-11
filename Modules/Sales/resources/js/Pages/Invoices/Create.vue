<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    persons: Array,
    products: Array,
});

// Helper to get today's date in YYYY-MM-DD format
const getTodayDate = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
};


const form = useForm({
    person_id: null,
    issue_date: getTodayDate(), // Default to today
    due_date: null,
    items: [],
});

const selectedProduct = ref(null);
const quantity = ref(1);

const totalAmount = computed(() => {
    return form.items.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
});

function addItem() {
    if (!selectedProduct.value || quantity.value <= 0) {
        alert('لطفا یک کالا انتخاب کرده و تعداد را مشخص کنید.');
        return;
    }

    const existingItem = form.items.find(item => item.product_id === selectedProduct.value.id);

    if (existingItem) {
        existingItem.quantity += parseInt(quantity.value, 10);
    } else {
        form.items.push({
            product_id: selectedProduct.value.id,
            name: selectedProduct.value.name,
            quantity: parseInt(quantity.value, 10),
            unit_price: selectedProduct.value.sale_price,
        });
    }

    selectedProduct.value = null;
    quantity.value = 1;
}

function removeItem(index) {
    form.items.splice(index, 1);
}

function submit() {
    if (!form.person_id) {
        alert('لطفا یک مشتری را انتخاب کنید.');
        return;
    }
    if (form.items.length === 0) {
        alert('فاکتور باید حداقل شامل یک قلم کالا باشد.');
        return;
    }
    form.post(route('invoices.store'));
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fa-IR').format(value);
};

</script>

<template>
    <Head title="صدور فاکتور جدید" />
    <AuthenticatedLayout>
        <h1 class="text-xl font-bold text-slate-800 mb-6">صدور فاکتور جدید</h1>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Customer and Dates Section -->
            <div class="card p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="person_id" class="block text-sm font-medium text-gray-700">مشتری</label>
                    <select id="person_id" v-model="form.person_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option :value="null" disabled>یک مشتری انتخاب کنید</option>
                        <option v-for="person in persons" :key="person.id" :value="person.id">{{ person.name }}</option>
                    </select>
                    <div v-if="form.errors.person_id" class="text-red-600 text-sm mt-1">{{ form.errors.person_id }}</div>
                </div>

                <div>
                    <label for="issue_date" class="block text-sm font-medium text-gray-700">تاریخ صدور</label>
                    <input type="date" id="issue_date" v-model="form.issue_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <div v-if="form.errors.issue_date" class="text-red-600 text-sm mt-1">{{ form.errors.issue_date }}</div>
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">تاریخ سررسید (اختیاری)</label>
                    <input type="date" id="due_date" v-model="form.due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <div v-if="form.errors.due_date" class="text-red-600 text-sm mt-1">{{ form.errors.due_date }}</div>
                </div>
            </div>

            <!-- Add Item Section -->
            <div class="card p-6">
                <h2 class="text-lg font-medium mb-4">افزودن کالا به فاکتور</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">انتخاب کالا</label>
                        <select v-model="selectedProduct" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option :value="null" disabled>یک کالا را انتخاب کنید</option>
                            <option v-for="product in products" :key="product.id" :value="product">
                                {{ product.name }} (قیمت: {{ formatCurrency(product.sale_price) }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">تعداد</label>
                        <input v-model.number="quantity" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <button type="button" @click="addItem" class="w-full btn-primary">افزودن</button>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="card p-0">
                <table class="min-w-full">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">کالا</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">تعداد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">قیمت واحد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">جمع</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in form.items" :key="index" class="border-t">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(item.unit_price) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(item.quantity * item.unit_price) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="form.items.length === 0">
                        <td colspan="5" class="text-center py-10 text-gray-500">هیچ کالایی به فاکتور اضافه نشده است.</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals and Submit Section -->
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold">
                    <span>جمع کل فاکتور: </span>
                    <span>{{ formatCurrency(totalAmount) }} تومان</span>
                </div>
                <div>
                    <button type="submit" class="btn-primary text-base" :disabled="form.processing">
                        ثبت نهایی فاکتور
                    </button>
                </div>
            </div>

        </form>
    </AuthenticatedLayout>
</template>

