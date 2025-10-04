<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::create([
            'name' => 'Menú',
            'description' => 'Control de acceso del menú',
            'key' => 'menu',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo de seguridad',
            'description' => 'Módulos de seguridad',
            'key' => 'seg',
            'user_id' => 1,
        ]);
    }
}
