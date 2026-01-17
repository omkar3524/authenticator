<?php

namespace Tests\Feature;

use App\Models\OauthClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SSOTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_redirects_to_login_if_not_authenticated()
    {
        $client = OauthClient::create([
            'name' => 'Test Client',
            'client_id' => (string) Str::uuid(),
            'client_secret' => Str::random(40),
            'redirect_uris' => ['https://client.com/callback'],
            'is_active' => true,
        ]);

        $response = $this->get('/authorize?' . http_build_query([
            'client_id' => (string) $client->client_id,
            'redirect_uri' => 'https://client.com/callback',
        ]));

        $response->assertRedirect(route('login'));
    }

    public function test_authorize_auto_redirects_with_token_if_authenticated()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $client = OauthClient::create([
            'name' => 'Test Client',
            'client_id' => (string) Str::uuid(),
            'client_secret' => Str::random(40),
            'redirect_uris' => ['https://client.com/callback'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/authorize?' . http_build_query([
            'client_id' => (string) $client->client_id,
            'redirect_uri' => 'https://client.com/callback',
        ]));

        $response->assertStatus(302);
        $this->assertStringContainsString('token=', $response->headers->get('Location'));
        $this->assertStringContainsString('https://client.com/callback', $response->headers->get('Location'));
    }
}
