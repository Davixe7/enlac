<?php

namespace Database\Seeders;

use App\Models\ActivityCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definir la ruta del archivo XLSX
        // Asume que el archivo está en 'database/seeders/activities_data.xlsx'
        $filePath = storage_path('app/private/activities2.xlsx');
        
        if (!file_exists($filePath)) {
            echo "ERROR: Archivo no encontrado en: {$filePath}. Asegúrate de colocar el XLSX allí.\n";
            return;
        }

        // Cargar los IDs de las tablas de categorías en memoria
        // (Esto es crucial para la eficiencia: evita N*2 consultas dentro del bucle)
        $planCategories = DB::table('plan_categories')->pluck('id', 'label')->all();
                            
        $activityCategories = DB::table('activity_categories')->pluck('id', 'label')->all();

        $activitiesToInsert = [];
        $lineNumber = 1;

        echo "Iniciando importación desde {$filePath}...\n";

        try {
            // 2. Leer el archivo con FastExcel y procesar cada línea
            (new FastExcel)
                ->import($filePath, function ($line) use (
                    &$activitiesToInsert, 
                    $planCategories, 
                    $activityCategories,
                    &$lineNumber
                ) {
                    $lineNumber++;

                    // Mapeo basado en el nombre exacto de la columna
                    $planLabel             = $line['PLAN'] ?? null;
                    $activityCategoryLabel = $line['CLASIFICACIÓN'] ?? null;
                    $name                  = $line['NOMBRE DE LA ACTIVIDAD'] ?? null;
                    $measurementUnit       = $line['UNIDAD DE MEDIDA'] ?? null;
                    $goalType              = $line['TIPO DE META'] ?? null;

                    // Omitir filas si no hay nombre o si la cabecera no se encontró
                    if (empty($name)) {
                        return; 
                    }

                    // Búsquedas de IDs
                    $planCategoryId = $planCategories[$planLabel] ?? null;
                    $activityCategoryId = $activityCategories[trim($activityCategoryLabel)] ?? null;

                    if(!$activityCategoryId){
                        $activityCategory = ActivityCategory::create([
                            'name'  => Str::slug($activityCategoryLabel, '_'),
                            'label' => $activityCategoryLabel
                        ]);
                        $activityCategoryId = $activityCategory->id;
                    }

                    // Validación y manejo de errores (en un Seeder usamos 'echo' para mostrar errores)
                    if (is_null($planCategoryId) || is_null($activityCategoryId)) {
                         if( is_null($planCategoryId) ){
                            echo "ADVERTENCIA (Línea {$lineNumber}): Saltando fila. Categoría de Plan ('{$planLabel}') no encontrada en DB.\n";
                            return;
                         }
                         echo "ADVERTENCIA (Línea {$lineNumber}): Saltando fila. Clasificación ('{$activityCategoryLabel}') no encontrada en DB.\n";
                         return;
                    }
                    
                    // 3. Acumular registro para la inserción masiva
                    $activitiesToInsert[] = [
                        'plan_category_id'      => $planCategoryId,
                        'activity_category_id'  => $activityCategoryId,
                        'name'                  => $name,
                        'measurement_unit'      => $measurementUnit,
                        'goal_type'             => $goalType,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                });

            // 4. Inserción masiva (Bulk Insert)
            $count = count($activitiesToInsert);
            if ($count > 0) {
                // Usamos una transacción por seguridad
                DB::transaction(function () use ($activitiesToInsert) {
                    DB::table('activities')->insert($activitiesToInsert);
                });
                echo "¡Importación finalizada con éxito! {$count} actividades insertadas.\n";
            } else {
                echo "No se encontraron actividades válidas para insertar.\n";
            }

        } catch (\Exception $e) {
            echo "ERROR FATAL: Error durante la importación en la línea {$lineNumber}: " . $e->getMessage() . "\n";
        }
    }
}
