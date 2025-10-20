<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use Illuminate\Database\Seeder;

class CooperativeProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cooperative::create([
            'name' => 'Cooperative',
            'user_id' => 3,
        ]);
    }
}
