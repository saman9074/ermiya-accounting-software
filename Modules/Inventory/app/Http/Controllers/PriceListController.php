<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\PriceList;

class PriceListController extends Controller
{
    public function index()
    {
        return Inertia::render('Inventory::PriceLists/Index', [
            'priceLists' => PriceList::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory::PriceLists/Create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:191|unique:price_lists,name']);
        PriceList::create($request->all());
        return redirect()->route('price-lists.index')->with('success', 'سطح قیمت با موفقیت ایجاد شد.');
    }

    public function edit(PriceList $priceList)
    {
        return Inertia::render('Inventory::PriceLists/Edit', ['priceList' => $priceList]);
    }

    public function update(Request $request, PriceList $priceList)
    {
        $request->validate(['name' => 'required|string|max:191|unique:price_lists,name,' . $priceList->id]);
        $priceList->update($request->all());
        return redirect()->route('price-lists.index')->with('success', 'سطح قیمت با موفقیت ویرایش شد.');
    }

    public function destroy(PriceList $priceList)
    {
        $priceList->delete();
        return redirect()->route('price-lists.index')->with('success', 'سطح قیمت با موفقیت حذف شد.');
    }
}
