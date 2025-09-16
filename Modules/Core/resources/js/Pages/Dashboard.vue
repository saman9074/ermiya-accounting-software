<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    stats: Object,
    recentInvoices: Array,
    chartData: Object, // <-- تغییر نام prop به chartData
});

// تغییر نام computed property به chartJsData برای جلوگیری از تداخل با prop
const chartJsData = computed(() => ({
    labels: props.chartData.labels,
    datasets: [
        {
            label: 'فروش ماهانه',
            backgroundColor: '#4c51bf',
            data: props.chartData.data,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
};
</script>

<template>
    <Head title="داشبورد" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">داشبورد</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <h3 class="text-gray-500 text-sm font-medium">درآمد امروز</h3>
                        <p class="text-2xl font-semibold">{{ Number(stats.incomeToday || 0).toLocaleString() }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <h3 class="text-gray-500 text-sm font-medium">هزینه امروز</h3>
                        <p class="text-2xl font-semibold">{{ Number(stats.expenseToday || 0).toLocaleString() }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <h3 class="text-gray-500 text-sm font-medium">فاکتورهای پرداخت نشده</h3>
                        <p class="text-2xl font-semibold">{{ Number(stats.overdueInvoices || 0).toLocaleString() }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">نمودار فروش ماهانه</h3>
                            <div v-if="chartData && chartData.labels && chartData.labels.length > 0" class="h-64">
                                <Bar :data="chartJsData" :options="chartOptions" />
                            </div>
                            <div v-else class="h-64 flex items-center justify-center text-center text-gray-500">
                                <p>داده ای برای نمایش نمودار وجود ندارد.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">آخرین فاکتورها</h3>
                            <ul>
                                <li v-for="invoice in recentInvoices" :key="invoice.id" class="flex justify-between py-2 border-b">
                                    <span>فاکتور #{{ invoice.id }} - {{ invoice.person.name }}</span>
                                    <span class="font-semibold">{{ Number(invoice.total_amount).toLocaleString() }}</span>
                                </li>
                                <li v-if="!recentInvoices || recentInvoices.length === 0" class="text-center text-gray-500 py-4">
                                    فاکتوری یافت نشد.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
