<?php

namespace App\Services;

use App\Models\OauthClient;
use Illuminate\Support\Str;

class ClientService
{
    public function validateClient(string $clientId, ?string $clientSecret = null): ?OauthClient
    {
        $query = OauthClient::where('client_id', $clientId)->where('is_active', true);

        if ($clientSecret) {
            $query->where('client_secret', $clientSecret);
        }

        return $query->first();
    }

    public function validateRedirectUri(OauthClient $client, string $redirectUri): bool
    {
        return in_array($redirectUri, $client->redirect_uris);
    }

    public function createClient(string $name, array $redirectUris, ?array $allowedScopes = []): OauthClient
    {
        return OauthClient::create([
            'name' => $name,
            'client_id' => (string) Str::uuid(),
            'client_secret' => Str::random(40),
            'redirect_uris' => $redirectUris,
            'allowed_scopes' => $allowedScopes,
            'is_active' => true,
        ]);
    }

    public function updateClient(OauthClient $client, array $data): bool
    {
        if (isset($data['providers'])) {
            $client->providers()->sync($data['providers']);
            unset($data['providers']);
        }
        return $client->update($data);
    }

    public function deactivateClient(OauthClient $client): bool
    {
        return $client->update(['is_active' => false]);
    }
}
