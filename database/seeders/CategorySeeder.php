<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; 

class CategorySeeder extends Seeder
{

    public function run(): void
    {

        Category::create([
            'name' => 'Frutas y Hortalizas',
            'description' => 'Productos frescos de temporada, cultivados localmente en Morelos.',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Granos y Semillas',
            'description' => 'Maíz, frijol, arroz, y otras semillas básicas para la alimentación.',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Lácteos y Derivados',
            'description' => 'Quesos, leche y otros productos lácteos artesanales.',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Bebidas y Jugos',
            'description' => 'Aguas frescas, jugos naturales y bebidas tradicionales de la región.',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Miel y Endulzantes',
            'description' => 'Miel pura de abeja y otros endulzantes naturales.',
            'is_active' => true,
        ]);
    }
}
