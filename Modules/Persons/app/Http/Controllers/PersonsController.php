<?php

namespace Modules\Persons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Persons\Models\Person;
use Modules\Persons\Models\PersonGroup;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\Transaction;
use Modules\Sales\Models\SalesReturn;

class PersonsController extends Controller
{
    public function index(Request $request)
    {
        $persons = Person::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->with('group')
            ->latest()
            ->paginate(15)
            ->withQueryString(); // برای حفظ پارامترهای جستجو در لینک‌های صفحه‌بندی

        return Inertia::render('Persons::Index', [
            'persons' => $persons,
            'filters' => $request->only(['search']), // ارسال فیلترهای فعلی به ویو
        ]);
    }

    public function create(): Response
    {
        // Pass person groups to the create view
        return Inertia::render('Persons::create', [
            'personGroups' => PersonGroup::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'person_group_id' => 'nullable|exists:person_groups,id',
        ]);

        Person::create($request->all());

        return redirect()->route('persons.index')->with('success', 'شخص جدید با موفقیت ایجاد شد.');
    }

    public function edit(Person $person): Response
    {
        // Pass person groups to the edit view
        return Inertia::render('Persons::edit', [
            'person' => $person,
            'personGroups' => PersonGroup::all(),
        ]);
    }

    public function update(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'person_group_id' => 'nullable|exists:person_groups,id',
        ]);

        $person->update($validated);

        return redirect()->route('persons.index')->with('success', 'اطلاعات شخص با موفقیت ویرایش شد.');
    }

    public function destroy(Person $person): RedirectResponse
    {
        $person->delete();

        return redirect()->route('persons.index')->with('success', 'شخص با موفقیت حذف شد.');
    }

    public function statement(Person $person)
    {
        // ۱. تمام فاکتورهای شخص را به همراه روابط واکشی می‌کنیم
        $invoices = $person->invoices()->with(['transactions', 'salesReturns'])->latest()->get();

        // --- شروع بلوک منطق نهایی و صحیح ---

        // ۲. محاسبه مقادیر نمایشی برای هر سطر فاکتور در جدول
        $invoices->each(function ($invoice) {
            $invoice->returned_amount = $invoice->salesReturns->sum('total_amount');
            $invoice->paid_positive_amount = $invoice->transactions->where('type', 'income')->where('amount', '>', 0)->sum('amount');
            $net_payable = $invoice->total_amount - $invoice->returned_amount;
            $invoice->invoice_balance = $net_payable - $invoice->paid_positive_amount;
        });

        // ۳. محاسبه مقادیر برای خلاصه نهایی کل حساب
        $total_invoices_amount = $invoices->sum('total_amount');

        // **مهم:** جمع جبری تمام تراکنش‌ها (پرداخت‌ها مثبت، برگشتی‌ها منفی)
        $net_transactions_amount = Transaction::where('transactionable_type', (new Invoice)->getMorphClass())
            ->whereIn('transactionable_id', $invoices->pluck('id'))
            ->where('type', 'income')
            ->sum('amount'); // sum تمام مقادیر (مثبت و منفی)

        $final_balance = $total_invoices_amount - $net_transactions_amount;

        // ۴. مقادیر نمایشی برای خلاصه حساب
        $display_total_paid = $invoices->pluck('transactions')->flatten()->where('type', 'income')->where('amount', '>', 0)->sum('amount');
        $display_total_returned = $invoices->sum('returned_amount');

        return Inertia::render('Persons::AccountStatement', [
            'person'           => $person,
            'invoices'         => $invoices,
            'total_invoices'   => $total_invoices_amount,
            'total_paid'       => $display_total_paid,
            'total_returned'   => $display_total_returned,
            'balance'          => $final_balance,
        ]);
    }
}

