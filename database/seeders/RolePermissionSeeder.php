<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('cipher', 'ADMIN')->first();
        $user = Role::where('cipher', 'USER')->first();
        $guest = Role::where('cipher', 'GUEST')->first();

        
        $allPermissions = Permission::all();
        $admin->permissions()->sync($allPermissions->pluck('id'));

        $userPermissions = Permission::whereIn('name', [
            'get-list-user',
            'read-user',
            'update-user',
        ])->pluck('id');

        $user->permissions()->sync($userPermissions);

        $guestPermissions = Permission::where('name', 'get-list-user')->pluck('id');

        $guest->permissions()->sync($guestPermissions);
    }
}