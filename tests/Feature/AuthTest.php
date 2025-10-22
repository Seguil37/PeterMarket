<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_registrarse_y_loguearse()
    {
        $registerUrl = $this->resolveUrl(['register', 'auth.register'], '/register', 'POST');
        $loginUrl    = $this->resolveUrl(['login', 'auth.login'], '/login', 'POST');
        $logoutUrl   = $this->resolveUrl(['logout', 'auth.logout'], '/logout', 'POST');

        // Acepta también /admin como destino válido
        $homeTargets = $this->resolveHomeTargets(['/home', '/dashboard', '/', '/admin']);

        $userEmail = 'test@example.com';
        $userPass  = 'password';

        // Registrar si existe endpoint, si no, crea el usuario con factory
        if ($registerUrl !== null) {
            $resp = $this->post($registerUrl, [
                'name' => 'Test User',
                'email' => $userEmail,
                'password' => $userPass,
                'password_confirmation' => $userPass,
            ]);

            $this->assertTrue(
                in_array($resp->getStatusCode(), [301, 302, 303, 307, 308]),
                'Se esperaba una redirección tras registrar (status 3xx). Obtenido: '.$resp->getStatusCode()
            );
            $this->assertRedirectToOneOf($resp, $homeTargets);
            $this->assertAuthenticated();

            $this->post($logoutUrl ?? '/logout');
            $this->assertGuest();
        } else {
            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => $userEmail,
                'password' => $userPass, // con cast "hashed" se encripta solo
            ]);
        }

        // Login
        $resp = $this->post($loginUrl ?? '/login', [
            'email' => $userEmail,
            'password' => $userPass,
        ]);

        $this->assertTrue(
            in_array($resp->getStatusCode(), [301, 302, 303, 307, 308]),
            'Se esperaba una redirección tras login (status 3xx). Obtenido: '.$resp->getStatusCode()
        );
        $this->assertRedirectToOneOf($resp, $homeTargets);
        $this->assertAuthenticated();
    }

    // ---------- Helpers ----------
    private function resolveUrl(array $routeNames, string $fallbackUri, string $method): ?string
    {
        foreach ($routeNames as $name) {
            if (Route::has($name)) {
                return route($name);
            }
        }
        return $this->routeUriExists($method, $fallbackUri) ? $fallbackUri : null;
    }

    private function routeUriExists(string $method, string $uri): bool
    {
        $method = strtoupper($method);
        $uri = ltrim($uri, '/');
        foreach (Route::getRoutes() as $route) {
            if (in_array($method, $route->methods(), true) && $route->uri() === $uri) {
                return true;
            }
        }
        return false;
    }

    private function resolveHomeTargets(array $fallbacks): array
    {
        $targets = [];
        foreach (['home', 'dashboard', 'admin'] as $name) {
            if (Route::has($name)) {
                $targets[] = route($name);
            }
        }
        foreach ($fallbacks as $url) {
            $targets[] = $url;
        }
        return array_values(array_unique($targets));
    }

    private function assertRedirectToOneOf($response, array $targets): void
    {
        $location = $response->headers->get('Location') ?? '';
        // Acepta tanto forma absoluta (http://localhost/...) como relativa (/admin)
        $urls = [];
        foreach ($targets as $t) {
            $urls[] = url($t);
            $urls[] = $t;
        }
        $this->assertTrue(
            in_array($location, $urls, true),
            'Se esperaba redirección a una de: ['.implode(', ', $targets)."] pero fue a: {$location}"
        );
    }
}
