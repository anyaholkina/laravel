<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('users_and_roles')->truncate(); 
    User::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $this->call([
        RoleSeeder::class,
        PermissionSeeder::class, 
        RolePermissionSeeder::class,
    ]);

    $admin = User::create([
        'username' => 'admin1Qwe112',
        'email' => 'admin114@example.com',
        'password' => 'password1A)_1253',
        'birthday' => '2000-09-08',
    ]);

    $adminRole = Role::where('cipher', 'ADMIN')->first();
    $admin->roles()->attach($adminRole->id);

    dd($admin->roles->first()->permissions->pluck('name'));
}}