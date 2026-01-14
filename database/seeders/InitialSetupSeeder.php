<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage_clients' => 'Manage Client Applications',
            'manage_providers' => 'Manage Social Providers',
            'manage_users' => 'Manage Users',
            'view_audits' => 'View Login Audits',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['label' => 'Standard User']);

        // Assign all permissions to admin
        $adminRole->permissions()->sync(Permission::all());

        // Create initial admin user (if not exists)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'), // Optional, but useful for initial setup
                'is_active' => true,
            ]
        );

        $admin->roles()->sync([$adminRole->id]);

        // Create default social providers (disabled by default)
        $providers = ['google', 'github', 'microsoft'];
        foreach ($providers as $provider) {
            SocialProvider::firstOrCreate(
                ['name' => $provider],
                [
                    'is_enabled' => false,
                    'client_id' => null,
                    'client_secret' => null,
                ]
            );
        }
    }
}
