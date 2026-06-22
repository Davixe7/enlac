<?php

namespace Database\Seeders;

use App\Models\Kardex;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class KardexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kardexes = [
            'default' => [
                ['order'=>1, 'name' => 'Acta de Nacimiento de'],
                ['order'=>2, 'name' => 'CURP'],
                ['order'=>3, 'name' => 'Cartilla de Vacunación'],
                ['order'=>4, 'name' => 'Comprobante de Domicilio'],
                ['order'=>5, 'name' => 'Número de Servicio Médico'],
                ['order'=>6, 'name' => 'INE Frente (Responsable del Beneficiario)'],
                ['order'=>7, 'name' => 'INE Atrás (Responsable del Beneficiario)'],
                ['order'=>8, 'name' => 'INE Frente (Tutor Legal)'],
                ['order'=>9, 'name' => 'INE Atrás (Tutor Legal)'],
            ],

            'tutor' => [
                ['order'=> 1,'template' => 'docx', 'name' => 'Reglamento para Padres de Familia'],
                ['order'=> 2,'template' => 'docx', 'name' => 'Carta de Consentimiento'],
                ['order'=> 3,'template' => 'docx', 'name' => 'Carta de Asignación del Responsable del Beneficiario'],
                ['order'=> 4,'template' => 'pdf',  'name' => 'Código de Ética'],
                ['order'=> 5,'template' => 'docx', 'name' => 'Autorización de Uso de Imagen y Pinturas'],
                ['order'=> 6,'template' => 'pdf',  'name' => 'Aviso de Privacidad'],
                ['order'=> 7,'template' => 'docx', 'name' => 'Autorización de Salidas'],
                ['order'=> 8,'template' => 'docx', 'name' => 'Acuerdo de No Divulgación'],
                ['order'=> 9,'template' => false,  'name' => 'Convenio de Pago Líderes ENLAC (sólo para Programa Asistido)'],
                ['order'=> 10,'template' => false,  'name' => 'Compromiso Previo de Pago (sólo para Programa Asistido)'],
                ['order'=> 11,'template' => false,  'name' => 'Certificado Médico'],
                ['order'=> 12,'template' => false,  'name' => 'Credencial de Discapacidad'],
                ['order'=> 13,'template' => false,  'name' => 'Tarjeta del Bienestar'],
                ['order'=> 14,'template' => false,  'name' => 'Estudio Socio-Económico'],
            ],

            'doctor' => [
                ['order'=>1, 'template' => 'docx', 'name' => 'Autorización de Equinoterapia'],
                ['order'=>2, 'template' => 'doc', 'name' => 'Autorización Médica para Realizar el Programa Físico'],
            ],

            'external' => [
                ['order'=>1, 'template' => 'docx', 'name' => 'Reglamento de Uso de Alberca'],
                ['order'=>2, 'template' => 'docx', 'name' => 'Reglamento para Usuarios Externos de Alberca'],
                ['order'=>3, 'template' => false, 'name' => 'Hoja de Datos Generales de Contacto'],
            ]
        ];

        foreach ($kardexes as $category => $items) {
            foreach($items as $index => $item){
                $kardex = Kardex::create([
                    'category'     => $category,
                    'order'        => $item['order'],
                    'name'         => $item['name'],
                    'slug'         => Str::slug($item['name']),
                    'has_template' => false,
                ]);

                $realIndex = $index + 1;
                $filename = Str::slug($item['name'], '-', 'es');
                $template_path = array_key_exists('template', $item) ? $item['template'] : null;
                $template_path = !$template_path ? $template_path : "kardexes/{$category}/{$realIndex}_{$filename}.{$template_path}";
                if( !$template_path || !Storage::exists($template_path) ){ continue; }

                $kardex
                ->addMediaFromDisk($template_path)
                ->preservingOriginal()
                ->toMediaCollection('template');
            }
        }
    }
}
