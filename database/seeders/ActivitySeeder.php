<?php

namespace Database\Seeders;

use App\Models\ActivityCategory;
use App\Models\PlanCategory;
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
        $filePath = storage_path('app/private/activities_full.xlsx');

        if (!file_exists($filePath)) {
            echo "ERROR: Archivo no encontrado en: {$filePath}. Asegúrate de colocar el XLSX allí.\n";
            return;
        }

        // Cargar los IDs de las tablas de categorías en memoria
        $categories         = PlanCategory::pluck('id', 'label');
        $subcategories      = ActivityCategory::pluck('id', 'label');
        $activitiesToInsert = [];
        $lineNumber         = 1;

        echo "Iniciando importación desde {$filePath}...\n";

        try {
            // 2. Leer el archivo con FastExcel y procesar cada línea
            (new FastExcel)
                ->import($filePath, function ($line) use (
                    &$activitiesToInsert,
                    $categories,
                    $subcategories,
                    &$lineNumber
                ) {
                    $lineNumber++;

                    // Mapeo basado en el nombre exacto de la columna
                    $categoryLabel    = $line['PLAN'] ?? null;
                    $subcategoryLabel = $line['CLASIFICACIÓN'] ?? null;
                    $name             = $line['NOMBRE DE LA ACTIVIDAD'] ?? null;
                    $measurementUnit  = $line['UNIDAD DE MEDIDA'] ?? null;
                    $goalType         = $line['TIPO DE META'] ?? null;

                    // A. Obtener o crear Categoría
                    $categoryId    = $categories->get($categoryLabel) ??
                    PlanCategory::firstOrCreate([
                        'label' => $categoryLabel,
                        'name'  => Str::slug($categoryLabel, '_')])->id;
                    $categories->put($categoryLabel, $categoryId);

                    // B. Obtener o crear Subcategoría
                    $subcategoryId = $subcategories->get($subcategoryLabel) ??
                    ActivityCategory::firstOrCreate([
                        'label'        => $subcategoryLabel,
                        'name'         => Str::slug($subcategoryLabel, '_'),
                        'parent_id'    => $categoryId])->id;
                    $subcategories->put($subcategoryLabel, $subcategoryId);

                    // Omitir filas si no hay nombre o si la cabecera no se encontró
                    if (empty($name)) {
                        return;
                    }

                    // Validación y manejo de errores (en un Seeder usamos 'echo' para mostrar errores)
                    if (is_null($categoryId) || is_null($subcategoryId)) {
                         echo is_null($categoryId)
                            ? "ADVERTENCIA (Línea {$lineNumber}): Saltando fila. Categoría de Plan ('{$categoryLabel}') no encontrada en DB.\n"
                            : "ADVERTENCIA (Línea {$lineNumber}): Saltando fila. Clasificación ('{$subcategoryLabel}') no encontrada en DB.\n";
                         return;
                    }

                    // 3. Acumular registro para la inserción masiva
                    $activitiesToInsert[] = [
                        'plan_category_id'      => $categoryId,
                        'activity_category_id'  => $subcategoryId,
                        'name'                  => $name,
                        'measurement_unit'      => $measurementUnit,
                        'goal_type'             => $goalType,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                });

            // 4. Inserción masiva (Bulk Insert)
            $count = count($activitiesToInsert);
            if ($count <= 0) {
                echo "No se encontraron actividades válidas para insertar.\n";
            }
            else {
                DB::transaction(function () use ($activitiesToInsert) {
                    DB::table('activities')->insert($activitiesToInsert);
                });
                echo "¡Importación finalizada con éxito! {$count} actividades insertadas.\n";
            }

        } catch (\Exception $e) {
            echo "ERROR FATAL: Error durante la importación en la línea {$lineNumber}: " . $e->getMessage() . "\n";
        }
    }
}
