<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $countryAdminRole = Role::firstOrCreate(['name' => 'country_admin']);
        $mediaBuyerRole = Role::firstOrCreate(['name' => 'media_buyer']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Optionally define permissions per domain area
        $permissions = [
            'view global dashboard',
            'manage countries',
            'manage ads',
            'manage invoices',
            'manage products',
            'view reports',
            'manage users',
            'view testing',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdminRole->givePermissionTo(Permission::all());
        $countryAdminRole->givePermissionTo([
            'manage ads',
            'manage invoices',
            'manage products',
            'view reports',
        ]);
        
        $mediaBuyerRole->givePermissionTo([
            'view testing',
        ]);

        // Create a default country to start with
        $country = Country::firstOrCreate([
            'code' => 'GLB',
        ], [
            'name' => 'Global',
            'currency' => 'USD',
            'timezone' => 'UTC',
            'active' => true,
        ]);

        // Create a default super admin if none exists
        if (! User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);

            $admin->assignRole($superAdminRole);

            // Attach to the default country for convenience
            $admin->countries()->syncWithoutDetaching([$country->id]);
        }
    }
}
