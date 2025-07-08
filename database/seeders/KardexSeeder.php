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
                ['required'=> false, 'name' => 'Acta de Nacimiento de'],
                ['required'=> false, 'name' => 'CURP'],
                ['required'=> false, 'name' => 'Cartilla de Vacunación'],
                ['required'=> false, 'name' => 'Comprobante de Domicilio'],
                ['required'=> false, 'name' => 'Número de Servicio Médico'],
                ['required'=> false, 'name' => 'INE Frente (Responsable del Beneficiario)'],
                ['required'=> false, 'name' => 'INE Atrás (Responsable del Beneficiario)'],
                ['required'=> false, 'name' => 'INE Frente (Tutor Legal)'],
                ['required'=> false, 'name' => 'INE Atrás (Tutor Legal)'],
            ],

            'tutor' => [
                ['required' => true, 'name' => 'Reglamento para Padres de Familia'],
                ['required' => true, 'name' => 'Carta de Consentimiento'],
                ['required' => true, 'name' => 'Carta de Asignación del Responsable del Beneficiario'],
                ['required' => true, 'name' => 'Código de Ética'],
                ['required' => true, 'name' => 'Autorización de Equinoterapia'],
                ['required' => true, 'name' => 'Autorización de Uso de Imagen y Pinturas'],
                ['required' => true, 'name' => 'Aviso de Privacidad'],
                ['required' => true, 'name' => 'Autorización de Salidas'],
                ['required' => true, 'name' => 'Acuerdo de No Divulgación'],
                ['required' => true, 'name' => 'Convenio de Pago Líderes ENLAC (sólo para Programa Asistido)'],
                ['required' => true, 'name' => 'Compromiso Previo de Pago (sólo para Programa Asistido)'],
            ],

            'doctor' => [
                ['required' => true, 'name' => 'Autorización de Equinoterapia'],
                ['required' => true, 'name' => 'Autorización Médica para Realizar el Programa Físico'],
            ],

            'external' => [
                ['required' => false, 'name' => 'Hoja de Datos Generales de Contacto'],
                ['required' => false, 'name' => 'Reglamento de Uso de Alberca'],
                ['required' => false, 'name' => 'Reglamento para Usuarios Externos de Alberca'],
            ]
        ];

        foreach ($kardexes as $category => $items) {
            foreach($items as $item){
                $kardex = Kardex::create([
                    'category' => $category,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'required' => $item['required'],
                    'has_template' => false
                ]);

                //$kardex
                //->addMediaFromDisk('template.pdf', 'local')
                //->preservingOriginal()
                //->toMediaCollection('template');
            }
        }
    }
}
