<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Show list of customers
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    // Show form to create a customer
    public function create()
    {
        return view('customers.create');
    }

    // Store new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string',
            'preferred_language' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    // Show form to edit customer
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Update customer data
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string',
            'preferred_language' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}

