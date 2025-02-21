<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Define customer permissions
        $customerPermissions = [
            'view_products',
            'view_own_profile',
            'update_own_profile',
            'create_orders',
            'delete_own_orders',
            'view_own_orders',
        ];

        // Create permissions if they don't exist
        foreach ($customerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Sync permissions to customer role
        $customerRole->syncPermissions($customerPermissions);

        // Create Super Admin if doesn't exist
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin'),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Create test customer if doesn't exist
        $customer = User::firstOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Client',
                'password' => Hash::make('client'),
            ]
        );
        $customer->assignRole('customer');
    }
}
