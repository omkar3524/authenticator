<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        // Store auth request data in session
        Session::put('auth_request', [
            'client_id' => $request->client_id,
            'redirect_uri' => $request->redirect_uri,
            'scope' => $request->scope,
        ]);

        // Redirect to a central login page where user chooses provider
        return redirect()->route('login');
    }
}
