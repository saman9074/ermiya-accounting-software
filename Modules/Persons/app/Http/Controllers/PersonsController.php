<?php

namespace Modules\Persons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Persons\Models\Person;
use Modules\Persons\Models\PersonGroup;

class PersonsController extends Controller
{
    public function index(): Response
    {
        // Eager load the group relationship for efficiency
        $persons = Person::with('group')->get();
        return Inertia::render('Persons::Index', ['persons' => $persons]);
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
}

