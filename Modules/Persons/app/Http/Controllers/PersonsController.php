<?php

namespace Modules\Persons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;
use Modules\Persons\Models\Person;

class PersonsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $persons = Person::all();
        return Inertia::render('Persons::Index', ['persons' => $persons]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Persons::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Person::create($request->all());

        return redirect()->route('persons.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): Response
    {
        $person = Person::findOrFail($id);

        return Inertia::render('Persons::edit', [
            'person' => $person,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $person = Person::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $person->update($validated);

        return redirect()->route('persons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $person = Person::findOrFail($id);
        $person->delete();

        return redirect()->route('persons.index');
    }
}
