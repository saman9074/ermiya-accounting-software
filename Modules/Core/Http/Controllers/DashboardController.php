<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Core\Models\FinancialYear;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ۱. ابتدا سال مالی فعال را پیدا کن
        $activeFinancialYear = FinancialYear::where('is_active', true)->first();

        // ۲. اگر هیچ سال مالی فعالی وجود نداشت، کاربر را هدایت کن
        if (!$activeFinancialYear) {
            // این شرط از بروز خطا در دیتابیس خالی جلوگیری می‌کند
            return redirect()->route('financial-years.create')
                ->with('info', 'لطفا ابتدا یک سال مالی ایجاد و آن را فعال کنید.');
        }

        // ۳. اگر سال مالی وجود داشت، محاسبات را انجام بده
        $startDate = $activeFinancialYear->start_date;
        $endDate = $activeFinancialYear->end_date;
        $today = Carbon::today(); // <--- تعریف متغیر $today

        $stats = [
            'incomeToday' => Transaction::where('type', 'income')->whereDate('transaction_date', $today)->whereBetween('transaction_date', [$startDate, $endDate])->sum('amount'),
            'overdueInvoices' => Invoice::where('status', '!=', 'paid')->where('due_date', '<', $today)->whereBetween('issue_date', [$startDate, $endDate])->sum(DB::raw('total_amount - paid_amount')),
            'expenseToday' => Transaction::where('type', 'expense')->whereDate('transaction_date', $today)->whereBetween('transaction_date', [$startDate, $endDate])->sum('amount'),
            'monthlyProfit' => 0, // Placeholder
        ];

        $recentInvoices = Invoice::with('person')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        $monthlySales = Invoice::select(
            DB::raw('YEAR(issue_date) as year, MONTH(issue_date) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare data for the chart
        $labels = $monthlySales->map(function ($item) {
            return Carbon::createFromDate($item->year, $item->month, 1)->format('F');
        });

        $data = $monthlySales->pluck('total');


        return Inertia::render('Core::Dashboard', [
            'stats' => $stats,
            'recentInvoices' => $recentInvoices,
            'chartData' => [
                'labels' => $labels,
                'data' => $data,
            ],
        ]);
    }
}
