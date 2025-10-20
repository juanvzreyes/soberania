<?php

namespace Database\Seeders;

use App\Models\Producer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProducerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producer::create([
            'certification' => 'Agricultura Sostenible, Buenas Prácticas Agrícolas (BPA), Agricultura Integrada',
            'user_id' => 2,
        ]);
    }
}
