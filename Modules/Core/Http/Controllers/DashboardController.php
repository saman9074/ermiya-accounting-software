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
        $activeYear = FinancialYear::getActiveYear();
        $stats = ['error' => null];

        if (!$activeYear) {
            $stats['error'] = 'هیچ سال مالی فعالی تعریف نشده است. لطفا ابتدا یک سال مالی فعال ایجاد کنید.';
        } else {
            // Stats calculation
            $today = now()->toDateString();
            $startDate = $activeYear->start_date;
            $endDate = $activeYear->end_date;

            $stats = [
                'incomeToday' => Transaction::where('type', 'income')->whereDate('transaction_date', $today)->whereBetween('transaction_date', [$startDate, $endDate])->sum('amount'),
                'overdueInvoices' => Invoice::where('payment_status', '!=', 'paid')->where('due_date', '<', $today)->whereBetween('issue_date', [$startDate, $endDate])->sum(DB::raw('total_amount - paid_amount')),
                // Add more stats as needed, e.g., expenses
                'expenseToday' => 0, // Placeholder
                'monthlyProfit' => 0, // Placeholder
            ];

            // Recent Invoices
            $recentInvoices = Invoice::with('person')
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->latest()
                ->take(5)
                ->get();

            // Chart Data for the last 30 days within the financial year
            $chartStartDate = now()->subDays(29)->max($startDate);
            $chartEndDate = now()->min($endDate);

            $salesData = Invoice::select(
                DB::raw('DATE(issue_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
                ->whereBetween('issue_date', [$chartStartDate, $chartEndDate])
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get()
                ->pluck('total', 'date');

            $chartLabels = [];
            $chartValues = [];
            $currentDate = Carbon::parse($chartStartDate);

            while ($currentDate <= $chartEndDate) {
                $dateString = $currentDate->toDateString();
                $chartLabels[] = jdate($dateString)->format('Y/m/d');
                $chartValues[] = $salesData[$dateString] ?? 0;
                $currentDate->addDay();
            }

            $chartData = [
                'labels' => $chartLabels,
                'values' => $chartValues,
            ];
        }


        return Inertia::render('Core::Dashboard', [
            'stats' => $stats,
            'recentInvoices' => $recentInvoices ?? [],
            'chartData' => $chartData ?? ['labels' => [], 'values' => []],
        ]);
    }
}
