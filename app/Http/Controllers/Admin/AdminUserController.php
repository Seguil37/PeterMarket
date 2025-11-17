<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::whereNotNull('admin_role')
            ->orderByDesc('is_admin')
            ->orderBy('name')
            ->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'admin_role' => ['required', Rule::in([User::ROLE_MASTER, User::ROLE_OPERATOR])],
        ]);

        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'is_admin'   => true,
            'admin_role' => $data['admin_role'],
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrador creado correctamente.');
    }

    public function edit(User $admin)
    {
        $this->ensureAdminAccount($admin);

        return view('admin.admins.edit', ['adminUser' => $admin]);
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        $this->ensureAdminAccount($admin);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'admin_role' => ['required', Rule::in([User::ROLE_MASTER, User::ROLE_OPERATOR])],
        ]);

        if ($admin->isMasterAdmin() && $data['admin_role'] !== User::ROLE_MASTER) {
            $this->ensureAnotherMasterExists($admin);
        }

        $admin->fill([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'admin_role' => $data['admin_role'],
        ]);

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrador actualizado correctamente.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        $this->ensureAdminAccount($admin);

        if (auth()->id() === $admin->id) {
            throw ValidationException::withMessages([
                'admin' => 'No puedes eliminar tu propia cuenta mientras estás conectado.',
            ]);
        }

        if ($admin->isMasterAdmin()) {
            $this->ensureAnotherMasterExists($admin);
        }

        $admin->delete();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Cuenta de administrador eliminada.');
    }

    public function toggleStatus(Request $request, User $admin): RedirectResponse
    {
        $this->ensureAdminAccount($admin);

        $data = $request->validate([
            'status' => ['required', Rule::in(['activate', 'deactivate'])],
        ]);

        if (auth()->id() === $admin->id) {
            throw ValidationException::withMessages([
                'status' => 'No puedes cambiar el estado de tu propia cuenta desde este módulo.',
            ]);
        }

        if ($data['status'] === 'deactivate') {
            if ($admin->isMasterAdmin()) {
                $this->ensureAnotherMasterExists($admin, 'status');
            }
            $admin->is_admin = false;
        } else {
            $admin->is_admin = true;
        }

        $admin->save();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', $data['status'] === 'deactivate'
                ? 'Administrador desactivado correctamente.'
                : 'Administrador activado correctamente.');
    }

    protected function ensureAdminAccount(User $user): void
    {
        if (is_null($user->admin_role)) {
            abort(404);
        }
    }

    protected function ensureAnotherMasterExists(User $excluding = null, string $field = 'admin_role'): void
    {
        $masters = User::where('is_admin', true)
            ->where('admin_role', User::ROLE_MASTER)
            ->when($excluding, fn ($query) => $query->where('id', '!=', $excluding->id))
            ->count();

        if ($masters === 0) {
            throw ValidationException::withMessages([
                $field => 'Debe existir al menos un Admin Master activo en el sistema.',
            ]);
        }
    }
}
