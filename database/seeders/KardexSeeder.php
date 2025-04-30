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
        $items = [
            // signed_by: null
            ['name' => 'Acta de Nacimiento de', 'signed_by' => null],
            ['name' => 'CURP', 'signed_by' => null],
            ['name' => 'Cartilla de Vacunación', 'signed_by' => null],
            ['name' => 'Comprobante de Domicilio', 'signed_by' => null],
            ['name' => 'Número de Servicio Médico', 'signed_by' => null],
            ['name' => 'INE Frente (Responsable del Beneficiario)', 'signed_by' => null],
            ['name' => 'INE Atrás (Responsable del Beneficiario)', 'signed_by' => null],
            ['name' => 'INE Frente (Tutor Legal)', 'signed_by' => null],
            ['name' => 'INE Atrás (Tutor Legal)', 'signed_by' => null],

            // signed_by: tutor
            ['name' => 'Reglamento para Padres de Familia', 'signed_by' => 'tutor'],
            ['name' => 'Carta de Consentimiento', 'signed_by' => 'tutor'],
            ['name' => 'Carta de Asignación del Responsable del Beneficiario', 'signed_by' => 'tutor'],
            ['name' => 'Código de Ética', 'signed_by' => 'tutor'],
            ['name' => 'Autorización de Equinoterapia', 'signed_by' => 'tutor'],
            ['name' => 'Autorización de Uso de Imagen y Pinturas', 'signed_by' => 'tutor'],
            ['name' => 'Aviso de Privacidad', 'signed_by' => 'tutor'],
            ['name' => 'Autorización de Salidas', 'signed_by' => 'tutor'],
            ['name' => 'Acuerdo de No Divulgación', 'signed_by' => 'tutor'],
            ['name' => 'Convenio de Pago Líderes ENLAC (sólo para Programa Asistido)', 'signed_by' => 'tutor'],
            ['name' => 'Compromiso Previo de Pago (sólo para Programa Asistido)', 'signed_by' => 'tutor'],

            // signed_by: doctor
            ['name' => 'Autorización de Equinoterapia', 'signed_by' => 'doctor'],
            ['name' => 'Autorización Médica para Realizar el Programa Físico', 'signed_by' => 'doctor'],

            // signed_by: external
            ['name' => 'Hoja de Datos Generales de Contacto', 'signed_by' => 'external'],
            ['name' => 'Reglamento de Uso de Alberca', 'signed_by' => 'external'],
            ['name' => 'Reglamento para Usuarios Externos de Alberca', 'signed_by' => 'external'],
        ];

        foreach ($items as $item) {
            Kardex::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'signed_by' => $item['signed_by'],
            ]);
        }
    }
}
