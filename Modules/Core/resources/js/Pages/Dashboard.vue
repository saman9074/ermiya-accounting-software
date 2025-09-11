<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const props = defineProps({
    stats: Object,
    recentInvoices: Array,
    chartData: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fa-IR').format(value || 0);
};

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                font: {
                    family: 'Vazirmatn'
                }
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                font: {
                    family: 'Vazirmatn'
                },
                callback: function(value) {
                    return formatCurrency(value);
                }
            }
        }
    },
    elements: {
        line: {
            tension: 0.3,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
        },
        point: {
            radius: 4,
            backgroundColor: '#3B82F6',
        }
    }
}));

const chartJsData = computed(() => ({
    labels: props.chartData.labels,
    datasets: [
        {
            label: 'فروش روزانه',
            data: props.chartData.values,
        }
    ]
}));

</script>

<template>
    <Head title="داشبورد" />
    <AuthenticatedLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-800">داشبورد</h1>
                <p class="text-sm text-slate-500">مرور وضعیت مالی و عملیات اخیر</p>
            </div>
            <div class="flex items-center gap-2">
                <Link :href="route('invoices.create')" class="btn-primary">صدور فاکتور جدید</Link>
            </div>
        </div>

        <!-- Alert for inactive financial year -->
        <div v-if="stats.error" class="mb-4 rounded-md bg-red-100 p-4 text-sm font-medium text-red-700">
            {{ stats.error }}
        </div>

        <template v-else>
            <!-- Stats Cards -->
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card">
                    <div class="text-slate-500">درآمد امروز</div>
                    <div class="mt-1">
                        <div class="text-2xl font-bold text-slate-800">{{ formatCurrency(stats.incomeToday) }} تومان</div>
                    </div>
                </div>
                <div class="card">
                    <div class="text-slate-500">هزینه امروز</div>
                    <div class="mt-1">
                        <div class="text-2xl font-bold text-slate-800">{{ formatCurrency(stats.expenseToday) }} تومان</div>
                    </div>
                </div>
                <div class="card">
                    <div class="text-slate-500">سود خالص ماه</div>
                    <div class="mt-1">
                        <div class="text-2xl font-bold text-slate-800">{{ formatCurrency(stats.monthlyProfit) }} تومان</div>
                    </div>
                </div>
                <div class="card">
                    <div class="text-slate-500">بدهی‌های معوق</div>
                    <div class="mt-1">
                        <div class="text-2xl font-bold text-slate-800">{{ formatCurrency(stats.overdueInvoices) }} تومان</div>
                    </div>
                </div>
            </section>

            <!-- Sales Chart and Recent Invoices -->
            <section class="mt-6 grid gap-4 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <h2 class="text-base font-semibold text-slate-800 mb-3">نمودار فروش ۳۰ روز گذشته</h2>
                    <div class="h-72">
                        <Line :data="chartJsData" :options="chartOptions" />
                    </div>
                </div>
                <div class="card">
                    <h2 class="mb-3 text-base font-semibold text-slate-800">فاکتورهای اخیر</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <tbody>
                            <tr v-for="invoice in recentInvoices" :key="invoice.id" class="border-b last:border-none">
                                <td class="py-2.5">
                                    <Link :href="route('invoices.show', invoice.id)" class="font-medium text-slate-700 hover:text-brand-accent">{{ invoice.person.name }}</Link>
                                    <div class="text-xs text-slate-500">{{ invoice.invoice_number }}</div>
                                </td>
                                <td class="py-2.5 text-left">
                                    <span>{{ formatCurrency(invoice.total_amount) }}</span>
                                    <div :class="{
                                            'badge-success': invoice.payment_status === 'paid',
                                            'badge-warning': invoice.payment_status === 'partial',
                                            'badge-danger': invoice.payment_status === 'unpaid',
                                        }">
                                        {{ invoice.payment_status === 'paid' ? 'پرداخت شده' : (invoice.payment_status === 'partial' ? 'پرداخت جزئی' : 'پرداخت نشده') }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="recentInvoices.length === 0">
                                <td colspan="2" class="py-4 text-center text-slate-500">فاکتوری یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </template>
    </AuthenticatedLayout>
</template>

