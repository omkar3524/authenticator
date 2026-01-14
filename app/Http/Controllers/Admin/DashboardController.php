<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OauthClient;
use App\Models\SocialProvider;
use App\Models\User;
use App\Models\LoginAudit;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'active_clients' => OauthClient::where('is_active', true)->count(),
                'enabled_providers' => SocialProvider::where('is_enabled', true)->count(),
                'recent_logins' => LoginAudit::where('status', 'success')->count(),
            ],
            'recent_audits' => LoginAudit::with('user')->latest()->take(10)->get(),
        ]);
    }
}
