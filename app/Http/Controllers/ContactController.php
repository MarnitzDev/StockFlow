<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();
        return Inertia::render('App/Contacts/Index', ['contacts' => $contacts]);
    }

    public function create()
    {
        return Inertia::render('App/Contacts/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:supplier,customer',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        Contact::create($validated);

        return redirect()->route('contacts.index')->with('message', 'Contact created successfully');
    }

    public function edit(Contact $contact)
    {
        return Inertia::render('App/Contacts/Edit', ['contact' => $contact]);
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:supplier,customer',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('message', 'Contact updated successfully');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('message', 'Contact deleted successfully');
    }

    public function show(Contact $contact)
    {
        $inventoryHistory = [];
        if ($contact->type === 'supplier') {
            $inventoryHistory = StockMovement::where('type', 'in')
                ->whereHas('product', function ($query) use ($contact) {
                    $query->where('supplier_id', $contact->id);
                })->get();
        } else {
            // For customers, you might want to implement a separate sales or order history system
            // This is just a placeholder
            $inventoryHistory = StockMovement::where('type', 'out')->get();
        }

        return Inertia::render('App/Contacts/Show', [
            'contact' => $contact,
            'inventoryHistory' => $inventoryHistory
        ]);
    }
}
