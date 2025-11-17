<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('is_admin', true)
            ->orderByDesc('is_master_admin')
            ->orderBy('name')
            ->paginate(12);

        $masterCount = User::where('is_master_admin', true)->count();

        return view('admin.admin-users.index', compact('admins', 'masterCount'));
    }

    public function create(): View
    {
        $masterCount = User::where('is_master_admin', true)->count();

        return view('admin.admin-users.create', compact('masterCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_master_admin' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $isMaster = (bool) $data['is_master_admin'];
        $isActive = (bool) $data['is_active'];

        if ($isMaster && User::where('is_master_admin', true)->exists()) {
            return back()
                ->withErrors(['is_master_admin' => 'Ya existe un Admin Master registrado.'])
                ->withInput();
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => true,
            'is_active' => $isActive,
            'is_master_admin' => $isMaster,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('status', 'Administrador creado correctamente.');
    }

    public function edit(User $admin): View
    {
        abort_unless($admin->is_admin, 404);

        $masterCount = User::where('is_master_admin', true)->count();

        return view('admin.admin-users.edit', compact('admin', 'masterCount'));
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_master_admin' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $isMaster = (bool) $data['is_master_admin'];
        $isActive = (bool) $data['is_active'];

        $masterCount = User::where('is_master_admin', true)->where('id', '!=', $admin->id)->count();
        if (!$isMaster && $admin->is_master_admin && $masterCount === 0) {
            return back()
                ->withErrors(['is_master_admin' => 'Debe existir al menos un Admin Master activo en el sistema.'])
                ->withInput();
        }

        if ($isMaster && $masterCount > 0) {
            return back()
                ->withErrors(['is_master_admin' => 'Ya existe otro Admin Master definido.'])
                ->withInput();
        }

        $admin->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $isActive,
            'is_master_admin' => $isMaster,
        ]);

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()->route('admin.admins.index')
            ->with('status', 'Administrador actualizado correctamente.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->is_admin, 404);

        $masterCount = User::where('is_master_admin', true)->where('id', '!=', $admin->id)->count();

        if ($admin->is_master_admin && $masterCount === 0) {
            return back()->withErrors(['delete' => 'No puedes eliminar al último Admin Master disponible.']);
        }

        if (auth()->id() === $admin->id) {
            return back()->withErrors(['delete' => 'No puedes eliminar tu propia cuenta mientras estás conectado.']);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('status', 'Administrador eliminado correctamente.');
    }
}
