<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('roles', 'socialAccounts')->latest()->paginate(20)
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'is_active' => 'required|boolean',
            'roles' => 'array',
        ]);

        $user->update(['is_active' => $data['is_active']]);

        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }
}
