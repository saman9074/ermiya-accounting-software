<?php

namespace Modules\Persons\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\PriceList;
use Modules\Persons\Models\PersonGroup;

class PersonGroupController extends Controller
{
    public function index()
    {
        return Inertia::render('Persons::PersonGroups/Index', [
            'personGroups' => PersonGroup::with('priceList')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Persons::PersonGroups/Create', [
            'priceLists' => PriceList::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:person_groups,name',
            'price_list_id' => 'nullable|exists:price_lists,id',
        ]);
        PersonGroup::create($request->all());
        return redirect()->route('person-groups.index')->with('success', 'گروه با موفقیت ایجاد شد.');
    }

    public function edit(PersonGroup $personGroup)
    {
        return Inertia::render('Persons::PersonGroups/Edit', [
            'personGroup' => $personGroup,
            'priceLists' => PriceList::all(),
        ]);
    }

    public function update(Request $request, PersonGroup $personGroup)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:person_groups,name,' . $personGroup->id,
            'price_list_id' => 'nullable|exists:price_lists,id',
        ]);
        $personGroup->update($request->all());
        return redirect()->route('person-groups.index')->with('success', 'گروه با موفقیت ویرایش شد.');
    }

    public function destroy(PersonGroup $personGroup)
    {
        $personGroup->delete();
        return redirect()->route('person-groups.index')->with('success', 'گروه با موفقیت حذف شد.');
    }
}
