<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return Inertia::render('Contacts/Customers/Index', ['customers' => $customers]);
    }

    public function create()
    {
        return Inertia::render('Contacts/Customers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('contacts.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return Inertia::render('Contacts/Customers/Show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Contacts/Customers/Edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('contacts.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('contacts.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
