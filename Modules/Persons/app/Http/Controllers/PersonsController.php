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
        // دریافت تمام فاکتورهای شخص
        $invoices = $person->invoices()->with('items')->get();

        $invoiceIds = $person->invoices()->pluck('id');
        $transactions = Transaction::whereIn('transactionable_id', $invoiceIds)
            ->where('transactionable_type', Invoice::class)
            ->get();
        $total_invoices = $invoices->sum('total_amount');
        $total_paid = $transactions->sum('amount'); // جمع جبری تراکنش‌ها
        $balance = $total_invoices - $total_paid;

        return Inertia::render('Persons::AccountStatement', [
            'person' => $person,
            'invoices' => $invoices,
            'balance' => $balance,
            'total_invoices' => $total_invoices,
            'total_paid' => $total_paid
        ]);
    }
}

