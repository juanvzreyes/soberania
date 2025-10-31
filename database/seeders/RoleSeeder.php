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

        $permissions = Permission::whereIn('module_key', ['menu', 'seg', 'categories', 'products', 'inventory', 'cart', 'checkout', 'orders', 'payments', 'deliveries', 'purchase-history', 'backups', 'reports-producer' ])->get();
        $admin->syncPermissions($permissions);

        $permissions = Permission::whereIn('module_key', ['producer', 'products', 'inventory', 'orders', 'payments', 'deliveries', 'reports-producer' ])->get();
        $producer->syncPermissions($permissions);

        $permissions = Permission::whereIn('module_key', ['cooperative', 'cart', 'checkout', 'purchase-history' ])->get();
        $permissions->push(Permission::where('name', 'menu.catalog')->first());
        $cooperative->syncPermissions($permissions);

        $permissions = Permission::whereIn('module_key', ['consumer', 'cart', 'checkout', 'purchase-history'])->get();
        $permissions->push(Permission::where('name', 'menu.catalog')->first());
        $consumer->syncPermissions($permissions);
    }
}
