<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        return Inertia::render('Inventory::Units/Index', [
            'units' => Unit::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory::Units/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'abbreviation' => 'required|string|max:191',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index');
    }

    public function edit(Unit $unit)
    {
        return Inertia::render('Inventory::Units/Edit', [
            'unit' => $unit,
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'abbreviation' => 'required|string|max:191',
        ]);

        $unit->update($request->all());

        return redirect()->route('units.index');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index');
    }
}
