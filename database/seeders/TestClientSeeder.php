<?php

namespace Database\Seeders;

use App\Models\OauthClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OauthClient::updateOrCreate(
            ['name' => 'Test App'],
            [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'redirect_uris' => ['https://google.com'], // Example redirect for manual testing
                'allowed_scopes' => ['user:read', 'user:write'],
                'is_active' => true,
            ]
        );
    }
}
