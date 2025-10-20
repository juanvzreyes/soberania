<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRoleToUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(1); // user admin
        $role = Role::find(1); // role admin
        $user->assignRole($role);

        $user = User::find(2); // user producer
        $role = Role::find(2); // role producer
        $user->assignRole($role);
    }
}
