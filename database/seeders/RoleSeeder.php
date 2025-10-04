<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Administrador',
        ]);

        $producer = Role::create([
            'name' => 'Producer',
            'description' => 'Productor',
        ]);

        $cooperative = Role::create([
            'name' => 'Cooperative',
            'description' => 'Cooperativa',
        ]);

        $consumer = Role::create([
            'name' => 'Consumer',
            'description' => 'Consumidor',
        ]);

        $permissions = Permission::whereIn('module_key', ['menu', 'seg'])->get();
        $admin->syncPermissions($permissions);
    }
}
