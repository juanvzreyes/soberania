<?php

namespace Database\Seeders;

use App\Models\Consumer;
use Illuminate\Database\Seeder;

class ConsumerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Consumer::create([
            'description' => 'Descripcion del consumidor',
            'user_id' => 4,
        ]);
    }
}
