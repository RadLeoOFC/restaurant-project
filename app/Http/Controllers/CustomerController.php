<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Show list of customers
    public function index()
    {
        $user = auth()->user();
    
        $customers = $user->hasRole('Admin')
            ? Customer::all()
            : $user->customers;
    
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
        $user = auth()->user();
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string',
            'preferred_language' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id', // только для админа
        ]);
    
        if (!$user->hasRole('Admin')) {
            // Обычный пользователь — можно создать только одного клиента
            if ($user->customer) {
                return redirect()->back()->with('error', __('messages.customer_already_exists'));
            }
            $validated['user_id'] = $user->id;
        } else {
            // Админ — может создать клиента, но ограничим возможность создания более одного клиента для обычных пользователей
            if (!empty($validated['user_id'])) {
                $targetUser = \App\Models\User::find($validated['user_id']);
    
                // Если целевой пользователь существует и это НЕ админ
                if ($targetUser && !$targetUser->hasRole('Admin')) {
                    // У него уже есть клиент — не даём создать ещё одного
                    if ($targetUser->customer) {
                        return redirect()->back()->withErrors([
                            'user_id' =>  __('messages.user_has_customer')
                        ]);
                    }
                }
            }
        }
    
        $customer = Customer::create($validated);
    
        return redirect()->route('customers.index')->with('success', __('messages.customer_created'));
    }       

    // Show form to edit customer
    public function edit(Customer $customer)
    {
        $user = auth()->user();
    
        if (!$user->hasRole('Admin') && $customer->user_id !== $user->id) {
            abort(403, 'Нет доступа к редактированию этого клиента.');
        }
    
        return view('customers.edit', compact('customer'));
    }
    

    // Update customer data
    public function update(Request $request, Customer $customer)
    {
        $user = auth()->user();
    
        if (!$user->hasRole('Admin') && $customer->user_id !== $user->id) {
            abort(403, 'Нет доступа к редактированию этого клиента.');
        }
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string',
            'preferred_language' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);
    
        if (!$user->hasRole('Admin')) {
            unset($validated['user_id']); // обычный пользователь не может менять
        } else {
            // если админ меняет user_id — нужно проверить, не будет ли дубликата
            if (
                isset($validated['user_id']) &&
                $validated['user_id'] != $customer->user_id // новое значение
            ) {
                $targetUser = \App\Models\User::find($validated['user_id']);
    
                if ($targetUser && !$targetUser->hasRole('Admin') && $targetUser->customer) {
                    return redirect()->back()->withErrors([
                        'user_id' => __('messages.user_has_customer')
                    ]);
                }
            }
        }
    
        $customer->update($validated);
    
        return redirect()->route('customers.index')->with('success', __('messages.customer_updated'));
    }    

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', __('messages.customer_deleted'));
    }
}

