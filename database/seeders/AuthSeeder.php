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

        $techUser1 = User::firstOrCreate(
            ['email' => 'tech@example.com'],
            ['name' => 'Tecnico1', 'password' => Hash::make('Password123!')]
        );
        $techUser2 = User::firstOrCreate(
            ['email' => 'tech2@example.com'],
            ['name' => 'Tecnico2', 'password' => Hash::make('Password123!')]
        );
        $techUser1->syncRoles(['Technician']);
        $techUser2->syncRoles(['Technician']);

        $reqUser1 = User::firstOrCreate(
            ['email' => 'cristian@example.com'],
            ['name' => 'Cristian Montecinos', 'password' => Hash::make('Password123!')]
        );
        $reqUser2 = User::firstOrCreate(
            ['email' => 'vianca@example.com'],
            ['name' => 'Vianca contreras', 'password' => Hash::make('Password123!')]
        );
        $reqUser1->syncRoles(['Requester']);
        $reqUser2->syncRoles(['Requester']);
    }
}
