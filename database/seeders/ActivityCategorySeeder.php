<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Maromas de Mesa',
            'Maromas de Piso',
            'Maromas Independientes',
            'Arrastre',
            'Gateo',
            'Caminar',
            'Carrera',
            'Excelencia Física',
            'Vestibulares',
            'Anti-gravedad',
            'Braquiación',
            'Patrones',
            'Bits',
            'Estimulación Visual',
            'Estimulación Auditiva',
            'Estimulación Tactil',
            'Máscara',
            'Manual',
            'VKE',
            'Cama Elástica',
            'Gimnasia',
            'Adaptación',
            'Principiantes/adaptacion al medio',
            'Crol/intermedio',
            'Dorso/intermedio',
            'Pecho/avanzados',
            'Mariposa/avanzados',
            'Equinoterapia',
            'Comunidad Infantil - Vida Práctica',
            'Comunidad Infantil - Alimentación',
            'Comunidad Infantil - Manipulativos',
            'Comunidad Infantil - Lenguaje',
            'Comunidad Infantil - Música',
            'Comunidad Infantil - Arte',
            'Casa de Niños - Vida Practica',
            'Casa de Niños - Sensorial',
            'Casa de Niños - Lenguaje',
            'Casa de Niños - Matematicas',
            'Casa de Niños - Ciencias: Biología',
            'Casa de Niños - Ciencias: Geografía e Historia',
            'Taller - Lenguaje',
            'Taller - Matemáticas',
            'Taller - Matemáticas: Memorización de la Suma',
            'Taller - Matemáticas: Memorización de la Resta',
            'Taller - Matemáticas: Memorización de la Multiplicación',
            'Taller - Matemáticas: Memorización de la División',
            'Taller - Geometría',
            'Taller - Geometría : Nomenclaturas Clasificada de Geometría',
            'Taller - Geometría: Del Sólido al Punto',
            'Taller - Geometría: Perlas Doradas',
            'Taller - Geometría: Análisis de la Expresión Gramatical',
            'Taller - Geometría: Relación Entre Dos Líneas',
            'Taller - Geometría: Tipos de Ángulos',
            'Taller - Geometría: Actividad de Exploración',
            'Taller - Historia',
            'Taller - Historia/grandes lecciones',
            'Taller - Geografía',
            'Taller - Geografía: Gabinete de Geometría',
            'Taller - Geografía: Nomenclatura Claisificada',
            'Taller - Geografía: Cartelones Impresionistas de Geografía',
            'Taller - Biología',
            'Programa Inicial - Gracia y cortesía',
        ];

        foreach($categories as $label){
            $data[] = [
                'name' => Str::slug($label, '_', 'es'),
                'label' => $label,
                'created_at' => now()
            ];
        }

        DB::table('activity_categories')->insert($data);
    }
}
