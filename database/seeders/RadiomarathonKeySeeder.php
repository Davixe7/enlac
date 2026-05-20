<?php

namespace Database\Seeders;

use App\Models\RadiomarathonKey;
use Illuminate\Database\Seeder;

class RadiomarathonKeySeeder extends Seeder
{
    public function run(): void
    {
        $keys = [
            // 1. DIA EVENTO
            ['code' => '1.1', 'classification' => 'Día Evento', 'concept' => 'Don. Pagados en efectivo', 'is_active' => true],
            ['code' => '1.2', 'classification' => 'Día Evento', 'concept' => 'Don. Pagados en Cheque', 'is_active' => true],
            ['code' => '1.3', 'classification' => 'Día Evento', 'concept' => 'Don. Pagados en dólares', 'is_active' => true],
            ['code' => '1.4', 'classification' => 'Día Evento', 'concept' => 'Don. Otras formas de pago', 'is_active' => true],
            ['code' => '1.5', 'classification' => 'Día Evento', 'concept' => 'Don. Pend. De pago SI sistema', 'is_active' => true],
            ['code' => '1.6', 'classification' => 'Día Evento', 'concept' => 'Don. Pend. De pago NO sistema', 'is_active' => true],

            // 2. PREVIOS
            ['code' => '2.1', 'classification' => 'Previos', 'concept' => 'Pagados antes del evento', 'is_active' => true],
            ['code' => '2.2', 'classification' => 'Previos', 'concept' => 'Pagados día evento', 'is_active' => true],
            ['code' => '2.3', 'classification' => 'Previos', 'concept' => 'Pendientes de pago', 'is_active' => true],
            ['code' => '2.4', 'classification' => 'Previos', 'concept' => 'Con respuesta día evento', 'is_active' => true],

            // 3. BOTEO
            ['code' => '3.1', 'classification' => 'Boteo', 'concept' => 'Boteo previo', 'is_active' => true],
            ['code' => '3.2', 'classification' => 'Boteo', 'concept' => 'Boteo cantado día evento', 'is_active' => true],
            ['code' => '3.3', 'classification' => 'Boteo', 'concept' => 'Boteo posterior al evento', 'is_active' => true],

            // 4. ESPECIE
            ['code' => '4.1', 'classification' => 'Especie', 'concept' => 'Don. Especie previo evento', 'is_active' => true],
            ['code' => '4.2', 'classification' => 'Especie', 'concept' => 'Don. Especie día evento', 'is_active' => true],
            ['code' => '4.3', 'classification' => 'Especie', 'concept' => 'Don. Especie posterior al evento', 'is_active' => true],

            // 5. INGRESOS POSTERIORES
            ['code' => '5.1', 'classification' => 'Ingresos Posteriores', 'concept' => 'Don. No ingresados al sistema', 'is_active' => true],
            ['code' => '5.2', 'classification' => 'Ingresos Posteriores', 'concept' => 'Don. Posteriores al evento', 'is_active' => true],
            ['code' => '5.3', 'classification' => 'Ingresos Posteriores', 'concept' => 'Ingr. Por venta de don. Especie', 'is_active' => true],
        ];

        foreach ($keys as $key) {
            RadiomarathonKey::create($key);
        }
    }
}
