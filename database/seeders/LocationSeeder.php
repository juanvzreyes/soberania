<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Neighborhood;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'Aguascalientes', 'code' => '01'],
            ['name' => 'Baja California', 'code' => '02'],
            ['name' => 'Baja California Sur', 'code' => '03'],
            ['name' => 'Campeche', 'code' => '04'],
            ['name' => 'Chiapas', 'code' => '05'],
            ['name' => 'Chihuahua', 'code' => '06'],
            ['name' => 'Coahuila', 'code' => '07'],
            ['name' => 'Colima', 'code' => '08'],
            ['name' => 'Ciudad de México', 'code' => '09'],
            ['name' => 'Durango', 'code' => '10'],
            ['name' => 'Guanajuato', 'code' => '11'],
            ['name' => 'Guerrero', 'code' => '12'],
            ['name' => 'Hidalgo', 'code' => '13'],
            ['name' => 'Jalisco', 'code' => '14'],
            ['name' => 'México', 'code' => '15'],
            ['name' => 'Michoacán', 'code' => '16'],
            ['name' => 'Morelos', 'code' => '17'],
            ['name' => 'Nayarit', 'code' => '18'],
            ['name' => 'Nuevo León', 'code' => '19'],
            ['name' => 'Oaxaca', 'code' => '20'],
            ['name' => 'Puebla', 'code' => '21'],
            ['name' => 'Querétaro', 'code' => '22'],
            ['name' => 'Quintana Roo', 'code' => '23'],
            ['name' => 'San Luis Potosí', 'code' => '24'],
            ['name' => 'Sinaloa', 'code' => '25'],
            ['name' => 'Sonora', 'code' => '26'],
            ['name' => 'Tabasco', 'code' => '27'],
            ['name' => 'Tamaulipas', 'code' => '28'],
            ['name' => 'Tlaxcala', 'code' => '29'],
            ['name' => 'Veracruz', 'code' => '30'],
            ['name' => 'Yucatán', 'code' => '31'],
            ['name' => 'Zacatecas', 'code' => '32'],
        ];

        DB::table('states')->insert($states);

        ini_set('memory_limit', '-1');
        $documento = glob('database/seeders/Excel/CPdescarga.xls');
        $data = Excel::toArray([], $documento[0]);

        for ($i = 0; $i < 32; $i++) {
            $skipHeader = true;
            foreach ($data[$i] as $row) {
                if ($skipHeader) {
                    $skipHeader = false;
                    continue;
                }

                if (!isset($row[0], $row[1], $row[3], $row[7]) || 
                    $row[0] === null || $row[1] === null || $row[3] === null || $row[7] === null) {
                    break;
                }

                $municipality = Municipality::firstOrCreate([
                    'name' => $row[3],
                    'code' => $row[11],
                    'state_id' => $row[7],
                ]);

                $neighborhood = new Neighborhood();
                $neighborhood->postal_code = $row[0];
                $neighborhood->name = $row[1];
                $neighborhood->municipality_id = $municipality->id;
                $neighborhood->save();
            }
        }
    }
}
