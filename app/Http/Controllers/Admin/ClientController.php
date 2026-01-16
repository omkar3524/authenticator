<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OauthClient;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        return Inertia::render('Admin/Clients/Index', [
            'clients' => OauthClient::with('providers')->latest()->get(),
            'providers' => \App\Models\SocialProvider::where('is_enabled', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|array',
            'redirect_uris.*' => 'required|url',
            'allowed_scopes' => 'nullable|array',
        ]);

        $this->clientService->createClient(
            $data['name'],
            $data['redirect_uris'],
            $data['allowed_scopes'] ?? []
        );

        return redirect()->back()->with('success', 'Client created successfully.');
    }

    public function update(Request $request, OauthClient $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|array',
            'redirect_uris.*' => 'required|url',
            'allowed_scopes' => 'nullable|array',
            'is_active' => 'required|boolean',
            'providers' => 'nullable|array',
            'providers.*' => 'exists:social_providers,id'
        ]);

        $this->clientService->updateClient($client, $data);

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    public function destroy(OauthClient $client)
    {
        $client->delete();
        return redirect()->back()->with('success', 'Client deleted successfully.');
    }
}
