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
                ['name' => 'Acta de Nacimiento de'],
                ['name' => 'CURP'],
                ['name' => 'Cartilla de Vacunación'],
                ['name' => 'Comprobante de Domicilio'],
                ['name' => 'Número de Servicio Médico'],
                ['name' => 'INE Frente (Responsable del Beneficiario)'],
                ['name' => 'INE Atrás (Responsable del Beneficiario)'],
                ['name' => 'INE Frente (Tutor Legal)'],
                ['name' => 'INE Atrás (Tutor Legal)'],
            ],

            'tutor' => [
                ['template' => 'docx', 'name' => 'Reglamento para Padres de Familia'],
                ['template' => 'docx', 'name' => 'Carta de Consentimiento'],
                ['template' => 'docx', 'name' => 'Carta de Asignación del Responsable del Beneficiario'],
                ['template' => 'pdf',  'name' => 'Código de Ética'],
                ['template' => 'docx', 'name' => 'Autorización de Uso de Imagen y Pinturas'],
                ['template' => 'pdf',  'name' => 'Aviso de Privacidad'],
                ['template' => 'docx', 'name' => 'Autorización de Salidas'],
                ['template' => 'docx', 'name' => 'Acuerdo de No Divulgación'],
                ['template' => false,  'name' => 'Convenio de Pago Líderes ENLAC (sólo para Programa Asistido)'],
                ['template' => false,  'name' => 'Compromiso Previo de Pago (sólo para Programa Asistido)'],
                ['template' => false,  'name' => 'Certificado Médico'],
                ['template' => false,  'name' => 'Credencial de Discapacidad'],
                ['template' => false,  'name' => 'Tarjeta del Bienestar'],
                ['template' => false,  'name' => 'Estudio Socio-Económico'],
            ],

            'doctor' => [
                ['template' => 'docx', 'name' => 'Autorización de Equinoterapia'],
                ['template' => 'doc', 'name' => 'Autorización Médica para Realizar el Programa Físico'],
            ],

            'external' => [
                ['template' => 'docx', 'name' => 'Reglamento de Uso de Alberca'],
                ['template' => 'docx', 'name' => 'Reglamento para Usuarios Externos de Alberca'],
                ['template' => false, 'name' => 'Hoja de Datos Generales de Contacto'],
            ]
        ];

        foreach ($kardexes as $category => $items) {
            foreach($items as $index => $item){
                $kardex = Kardex::create([
                    'category' => $category,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'has_template' => false
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
