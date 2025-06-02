<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Администратор системы',
                'cipher' => 'ADMIN',
            ],
            [
                'name' => 'User',
                'description' => 'Обычный пользователь',
                'cipher' => 'USER',
            ],
            [
                'name' => 'Guest',
                'description' => 'Гость',
                'cipher' => 'GUEST',
            ],
        ];

        foreach ($roles as $role) {
            
            Role::updateOrCreate(
                ['cipher' => $role['cipher']],  
                $role  
            );
        }
    }
}