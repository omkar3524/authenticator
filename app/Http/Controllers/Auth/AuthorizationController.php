<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends Controller
{
    protected $clientService;
    protected $authService;

    public function __construct(ClientService $clientService, AuthService $authService)
    {
        $this->clientService = $clientService;
        $this->authService = $authService;
    }

    public function authorize(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'redirect_uri' => 'required|url',
            'scope' => 'nullable|string',
        ]);

        $client = $this->clientService->validateClient($request->client_id);

        if (!$client) {
            $this->authService->logAudit(null, $request->client_id, 'failure', 'Invalid client ID');
            return response()->json(['error' => 'invalid_client'], 401);
        }

        if (!$this->clientService->validateRedirectUri($client, $request->redirect_uri)) {
            $this->authService->logAudit(null, $request->client_id, 'failure', 'Invalid redirect URI: ' . $request->redirect_uri);
            return response()->json(['error' => 'invalid_redirect_uri'], 401);
        }

        // SSO Check: If user is already logged in, skip the login page
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account is deactivated.');
            }

            // Issue token immediately
            $token = $this->authService->issueToken($user, $client->client_id, explode(',', $request->scope ?? ''));
            $this->authService->logAudit($user->id, $client->client_id, 'success', 'SSO Login');

            // Redirect back to client with token
            $separator = str_contains($request->redirect_uri, '?') ? '&' : '?';
            return redirect($request->redirect_uri . $separator . 'token=' . $token);
        }

        // Store auth request data in session
        Session::put('auth_request', [
            'client_id' => $request->client_id,
            'redirect_uri' => $request->redirect_uri,
            'scope' => $request->scope,
        ]);

        // Redirect to a central login page where user chooses provider
        return redirect()->route('auth');
    }
}
