<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialProvider;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginWithPassword(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showLoginForm()
    {
        return inertia('auth/Login');
    }

    public function selectProvider()
    {
        $providers = SocialProvider::where('is_enabled', true)->get();
        return inertia('auth/SelectProvider', [
            'providers' => $providers
        ]);
    }

    public function redirectToProvider($provider)
    {
        $this->configureProvider($provider);
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $this->configureProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            $this->authService->logAudit(null, null, 'failure', 'Social login failed for ' . $provider . ': ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed.');
        }

        /** @var \Laravel\Socialite\Two\User $socialUser */
        $user = $this->authService->findOrCreateUser($socialUser, $provider);

        if (!$user->is_active) {
            $this->authService->logAudit($user->id, null, 'failure', 'Account deactivated');
            return redirect()->route('login')->with('error', 'Your account is deactivated.');
        }

        // Login user to establish session (Critical for SSO)
        Auth::login($user);

        // Check for pending auth request
        $authRequest = Session::get('auth_request');

        if ($authRequest) {
            $token = $this->authService->issueToken($user, $authRequest['client_id'], explode(',', $authRequest['scope'] ?? ''));
            $this->authService->logAudit($user->id, $authRequest['client_id'], 'success');

            Session::forget('auth_request');

            // Redirect back to client with token
            $separator = str_contains($authRequest['redirect_uri'], '?') ? '&' : '?';
            return redirect($authRequest['redirect_uri'] . $separator . 'token=' . $token);
        }

        // If no auth request, it's a local login
        $this->authService->logAudit($user->id, null, 'success', 'Local login');

        return redirect()->route('dashboard');
    }

    protected function configureProvider($provider)
    {
        $config = SocialProvider::where('name', $provider)->first();

        if ($config && $config->client_id && $config->client_secret) {
            Config::set("services.{$provider}.client_id", $config->client_id);
            Config::set("services.{$provider}.client_secret", $config->client_secret);
            Config::set("services.{$provider}.redirect", route('login.callback', $provider));
        }
    }
}
