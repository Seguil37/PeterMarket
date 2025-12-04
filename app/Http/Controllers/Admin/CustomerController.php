<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $customersQuery = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->orderByDesc('orders_count');

        if ($search !== '') {
            $customersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $customersQuery->paginate(12)->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function show(User $customer)
    {
        abort_if($customer->is_admin, 404);

        $orders = $customer->orders()->with('items')->latest()->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function update(Request $request, User $customer)
    {
        abort_if($customer->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customer->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('ok', 'Perfil de cliente actualizado.');
    }
}
