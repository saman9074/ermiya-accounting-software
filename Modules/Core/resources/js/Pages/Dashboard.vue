<script setup>
import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
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
                <button class="btn-primary">ثبت سند جدید</button>
            </div>
        </div>

        <!-- Stats Cards -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card">
                <div class="flex items-center justify-between text-slate-500">
                    <div>درآمد امروز</div>
                    <div class="text-sm">👁</div>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <div class="text-2xl font-bold text-slate-800">۳,۵۲۰,۰۰۰ تومان</div>
                    <span class="badge-success">+ ۱۲%</span>
                </div>
            </div>
            <div class="card">
                <div class="flex items-center justify-between text-slate-500">
                    <div>هزینه امروز</div>
                    <div class="text-sm">💸</div>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <div class="text-2xl font-bold text-slate-800">۱,۲۴۰,۰۰۰ تومان</div>
                    <span class="badge-danger">+ ۵%</span>
                </div>
            </div>
            <div class="card">
                <div class="flex items-center justify-between text-slate-500">
                    <div>سود خالص ماه</div>
                    <div class="text-sm">📈</div>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <div class="text-2xl font-bold text-slate-800">۸,۴۲۰,۰۰۰ تومان</div>
                    <span class="badge-success">+ ۷%</span>
                </div>
            </div>
            <div class="card">
                <div class="flex items-center justify-between text-slate-500">
                    <div>بدهی‌های معوق</div>
                    <div class="text-sm">⏳</div>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <div class="text-2xl font-bold text-slate-800">۵۴۰,۰۰۰ تومان</div>
                    <span class="badge-warning">+ ۲%</span>
                </div>
            </div>
        </section>

        <!-- Recent Invoices and Shortcuts -->
        <section class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <h2 class="text-base font-semibold text-slate-800 mb-3">فاکتورهای اخیر</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">شماره</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مشتری</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">تاریخ</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">مبلغ</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">وضعیت</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">INV-1403-001</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">شرکت توسعه آبان</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">۱۴۰۳/۰۶/۲۳</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">۱,۸۵۰,۰۰۰</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm"><span class="badge-success">پرداخت شد</span></td>
                        </tr>
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">INV-1403-002</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">فروشگاه دماوند</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">۱۴۰۳/۰۶/۲۴</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">۷۶۰,۰۰۰</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm"><span class="badge-danger">معوق</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h2 class="mb-3 text-base font-semibold text-slate-800">میانبرها</h2>
                <div class="grid grid-cols-2 gap-3">
                    <button class="btn-primary">دریافت وجه</button>
                    <button class="btn-primary">پرداخت وجه</button>
                    <button class="btn-primary">گزارش فروش</button>
                </div>
            </div>
        </section>

    </AuthenticatedLayout>
</template>
