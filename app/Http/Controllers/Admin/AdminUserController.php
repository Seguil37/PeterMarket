<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::where('is_admin', true)
            ->orderByDesc('is_master_admin')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'is_admin'        => true,
            'is_active'       => $request->boolean('is_active', true),
            'is_master_admin' => $request->boolean('is_master_admin'),
        ]);

        return redirect()->route('admin.admins.index')->with('ok', 'Administrador creado correctamente.');
    }

    public function edit(User $admin)
    {
        abort_unless($admin->is_admin, 404);

        return view('admin.users.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        abort_unless($admin->is_admin, 404);

        $data = $this->validatePayload($request, $admin->id, false);

        $payload = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($data['password']);
        }

        $newIsMaster = $request->boolean('is_master_admin');

        if ($admin->id === $request->user()->id) {
            $newIsMaster = true;
        } elseif ($admin->is_master_admin && !$newIsMaster && $this->countMastersExcluding($admin->id) === 0) {
            return back()
                ->withErrors(['is_master_admin' => 'Debe existir al menos un Admin Master activo.'])
                ->withInput();
        }

        $payload['is_master_admin'] = $newIsMaster;

        $admin->update($payload);

        return redirect()->route('admin.admins.index')->with('ok', 'Administrador actualizado correctamente.');
    }

    public function destroy(Request $request, User $admin)
    {
        abort_unless($admin->is_admin, 404);

        if ($admin->id === $request->user()->id) {
            return back()->withErrors(['general' => 'No puedes eliminar tu propia cuenta.']);
        }

        if ($admin->is_master_admin && $this->countMastersExcluding($admin->id) === 0) {
            return back()->withErrors(['general' => 'Debe permanecer al menos un Admin Master.']);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('ok', 'Administrador eliminado correctamente.');
    }

    protected function validatePayload(Request $request, ?int $ignoreId = null, bool $requirePassword = true): array
    {
        $passwordRules = $requirePassword ? ['required','string','min:8','confirmed'] : ['nullable','string','min:8','confirmed'];

        return $request->validate([
            'name'            => ['required','string','max:255'],
            'email'           => ['required','email','max:255', Rule::unique('users', 'email')->ignore($ignoreId)],
            'password'        => $passwordRules,
            'is_active'       => ['nullable','boolean'],
            'is_master_admin' => ['nullable','boolean'],
        ]);
    }

    protected function countMastersExcluding(int $userId): int
    {
        return User::where('is_admin', true)
            ->where('is_master_admin', true)
            ->where('id', '!=', $userId)
            ->count();
    }
}
