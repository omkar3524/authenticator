<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use App\Models\LoginAudit;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Two\User as SocialiteUser;
use Illuminate\Support\Str;

class AuthService
{
    public function findOrCreateUser(SocialiteUser $socialUser, string $provider): User
    {
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;

            // Update tokens
            $socialAccount->update([
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);

            return $user;
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $socialUser->getEmail(),
                'password' => null, // Social only
                'is_active' => true,
            ]);
        }

        // Link account
        $user->socialAccounts()->create([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken,
        ]);

        return $user;
    }

    public function logAudit(?int $userId, ?string $clientId, string $status, ?string $details = null): void
    {
        LoginAudit::create([
            'user_id' => $userId,
            'client_id' => $clientId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
            'details' => $details,
        ]);
    }

    public function issueToken(User $user, string $clientId, array $scopes = []): string
    {
        return $user->createToken($clientId, $scopes)->plainTextToken;
    }
}
