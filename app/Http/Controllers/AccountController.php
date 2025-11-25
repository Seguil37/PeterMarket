<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->paginate(8);

        return view('account.dashboard', compact('user', 'orders'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = $request->string('password')->toString();
        $user->save();

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}
