<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login para clientes.
     */
    public function showCustomerLogin()
    {
        if (Auth::check()) {
            return Auth::user()->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect()->route('catalog.index');
        }

        return view('auth.login');
    }

    /**
     * Procesa el login de clientes.
     */
    public function customerLogin(Request $request)
    {
        $cred = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt(array_merge($cred, ['is_admin' => false]), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('catalog.index'));
        }

        return back()->withErrors(['email' => 'Credenciales inválidas o acceso no autorizado.'])->onlyInput('email');
    }

    /**
     * Muestra el formulario de login para administradores.
     */
    public function showAdminLogin()
    {
        if (Auth::check()) {
            return Auth::user()->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect()->route('catalog.index');
        }

        return view('admin.auth.login');
    }

    /**
     * Procesa el login de administradores.
     */
    public function adminLogin(Request $request)
    {
        $cred = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $adminCredentials = array_merge($cred, [
            'is_admin' => true,
            'is_active' => true,
        ]);

        if (Auth::attempt($adminCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciales inválidas o acceso no autorizado.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $redirectRoute = 'login';
        if (Auth::check()) {
            $redirectRoute = Auth::user()->is_admin ? 'admin.login' : 'catalog.index';
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute);
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect()->route('catalog.index');
        }

        return view('auth.register');
    }

    // Procesar registro de cliente
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
            'is_active' => true,
            'is_master_admin' => false,
        ]);

        Auth::login($user);

        return redirect()->route('catalog.index')->with('success','Bienvenido. Has iniciado sesión.');
    }
}
