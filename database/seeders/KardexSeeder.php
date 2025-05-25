<?php

namespace Database\Seeders;

use App\Models\Kardex;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KardexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kardexes = [
            'default' => [
                'Acta de Nacimiento de',
                'CURP',
                'Cartilla de Vacunación',
                'Comprobante de Domicilio',
                'Número de Servicio Médico',
                'INE Frente (Responsable del Beneficiario)',
                'INE Atrás (Responsable del Beneficiario)',
                'INE Frente (Tutor Legal)',
                'INE Atrás (Tutor Legal)',
            ],

            'tutor' => [
                'Reglamento para Padres de Familia *',
                'Carta de Consentimiento *',
                'Carta de Asignación del Responsable del Beneficiario *',
                'Código de Ética *',
                'Autorización de Equinoterapia *',
                'Autorización de Uso de Imagen y Pinturas *',
                'Aviso de Privacidad *',
                'Autorización de Salidas *',
                'Acuerdo de No Divulgación *',
                'Convenio de Pago Líderes ENLAC (sólo para Programa Asistido) *',
                'Compromiso Previo de Pago (sólo para Programa Asistido) *'
            ],

            'doctor' => [
                'Autorización de Equinoterapia',
                'Autorización Médica para Realizar el Programa Físico',
            ],

            'externo' => [
                'Hoja de Datos Generales de Contacto',
                'Reglamento de Uso de Alberca',
                'Reglamento para Usuarios Externos de Alberca',
            ]
        ];

        foreach ($kardexes as $category => $items) {
            foreach($items as $item){
                Kardex::create([
                    'category' => $category,
                    'name' => $item,
                    'slug' => Str::slug($item),
                    'required' => false
                ]);
            }
        }
    }
}
