<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // --- Permissions  ---
        $permissions = [
            'ticket.viewAny',
            'ticket.view',
            'ticket.create',
            'ticket.update',
            'ticket.assign',
            'ticket.close',
            'attachment.view',
            'attachment.upload',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Roles ---
        $admin      = Role::firstOrCreate(['name' => 'Admin']);
        $technician = Role::firstOrCreate(['name' => 'Technician']);
        $requester  = Role::firstOrCreate(['name' => 'Requester']);


        $admin->givePermissionTo($permissions);

        $technician->givePermissionTo([
            'ticket.viewAny',
            'ticket.view',
            'ticket.create',
            'ticket.update',
            'ticket.assign',
            'ticket.close',
            'attachment.view',
            'attachment.upload',
        ]);

        $requester->givePermissionTo([
            'ticket.viewAny',
            'ticket.view',
            'ticket.create',
            'attachment.view',
        ]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('Password123!')]
        );
        $adminUser->syncRoles(['Admin']);

        $techUser = User::firstOrCreate(
            ['email' => 'tech@example.com'],
            ['name' => 'Tech User', 'password' => Hash::make('Password123!')]
        );
        $techUser->syncRoles(['Technician']);

        $reqUser = User::firstOrCreate(
            ['email' => 'requester@example.com'],
            ['name' => 'Requester User', 'password' => Hash::make('Password123!')]
        );
        $reqUser->syncRoles(['Requester']);
    }
}
