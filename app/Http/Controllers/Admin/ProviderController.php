<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Providers/Index', [
            'providers' => SocialProvider::all()
        ]);
    }

    public function update(Request $request, SocialProvider $provider)
    {
        $data = $request->validate([
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'is_enabled' => 'required|boolean',
        ]);

        $provider->update($data);

        return redirect()->back()->with('success', 'Provider updated successfully.');
    }
}
