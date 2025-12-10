<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $currencies = \App\Models\Currency::all();
        $users = \App\Models\User::all();
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('currencies', 'users', 'settings'));
    }

    public function storeCurrency(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies',
            'symbol' => 'required|string|max:5',
            'rate' => 'required|numeric|min:0',
        ]);

        \App\Models\Currency::create($validated);
        \Illuminate\Support\Facades\Cache::forget('currency_symbol');

        return back()->with('success', 'Currency added successfully.');
    }
    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Company information updated.');
    }

    public function defaultCurrency(\App\Models\Currency $currency)
    {
        \App\Models\Currency::query()->update(['is_default' => false]);
        $currency->update(['is_default' => true]);

        \Illuminate\Support\Facades\Cache::forget('currency_symbol');

        return back()->with('success', 'Default currency updated.');
    }

    public function destroyCurrency(\App\Models\Currency $currency)
    {
        if ($currency->is_default) {
            return back()->withErrors(['error' => 'Cannot delete default currency.']);
        }
        $currency->delete();
        \Illuminate\Support\Facades\Cache::forget('currency_symbol');
        return back()->with('success', 'Currency deleted.');
    }

    public function createUser()
    {
        return view('users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.index')->with('success', 'User created successfully.');
    }

    public function destroyUser(\App\Models\User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'Cannot delete yourself.']);
        }

        // Check for dependencies
        if ($user->orders()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete user. They have associated orders.']);
        }

        // Add other checks if needed (e.g. work orders)
        // if ($user->workOrders()->exists()) ...

        try {
            $user->delete();
            return back()->with('success', 'User deleted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }
}
