<?php
namespace App\Services;

use App\Models\Candidate;
use App\Models\PaymentConfig;
use App\Models\PaymentDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SemaforoService
{
    /**
     * Genera la matriz de 12 meses escolares para un Candidato en un año escolar específico.
     */
    public function generateMatrix(int $candidateId, int $schoolYear): array
    {
        $candidate = Candidate::findOrFail($candidateId);

        // 1. Definir rango calendario del año escolar (Agosto Año Escolar hasta Julio Año Siguiente)
        $startCalendarDate = Carbon::createFromDate($schoolYear, 8, 1)->startOfDay();
        $endCalendarDate   = Carbon::createFromDate($schoolYear + 1, 7, 31)->endOfDay();

        // 2. Obtener todos los detalles de pago del candidato en ese rango de fechas
        $paymentDetails = PaymentDetail::where('candidate_id', $candidateId)
            ->where(function ($query) use ($schoolYear) {
                $query->where(function ($q) use ($schoolYear) {
                    $q->where('year', $schoolYear)->where('month', '>=', 8);
                })->orWhere(function ($q) use ($schoolYear) {
                    $q->where('year', $schoolYear + 1)->where('month', '<=', 7);
                });
            })->get();

        // 3. Obtener los Snapshots activos que intersecten el año escolar
        $allConfigs = PaymentConfig::where('candidate_id', $candidateId)
            ->where('effective_since', '<=', $endCalendarDate)
            ->where(function ($q) use ($startCalendarDate) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $startCalendarDate);
            })
            ->get();

        // Agrupamos directamente por el ID de la configuración central (el patrocinio real)
        $configsBySponsorship = $allConfigs->groupBy('sponsorship_id');

        $matrix = [];

        // 4. Iterar por cada configuración de pago (Patrocinio activo o histórico) del candidato
        foreach ($configsBySponsorship as $sponsorshipId => $configs) {
            $monthsData = [];

            for ($iSchoolMonth = 1; $iSchoolMonth <= 12; $iSchoolMonth++) {

                $iMonthCalendarDate = fromSchoolMonth($iSchoolMonth, $schoolYear);
                $realMonth = $iMonthCalendarDate->month;
                $realYear  = $iMonthCalendarDate->year;

                // Encontrar el snapshot aplicable para este mes calendario específico
                $currentConfig = $configs->first(function ($snap) use ($sponsorshipId, $iMonthCalendarDate, $iSchoolMonth) {
                    return $sponsorshipId == $snap->sponsorship_id && $iMonthCalendarDate->between($snap->effective_since, $snap->effective_until ?? now()->addYears(100));
                });

                if (!$currentConfig) {
                    $monthsData[$iSchoolMonth] = $this->buildMonthPayload($currentConfig,0, 0, 'white', $realMonth, $realYear);
                    continue;
                }

                // Sumar aportes específicos para este mes/año asociados a los pagos de este snapshot
                $paidAmount = $paymentDetails->where('payment_config_id', $currentConfig->id)
                ->where('year', $realYear)
                ->where('month', $realMonth)
                ->sum('amount');

                $goalAmount = $currentConfig->monthlyAmount;
                $color = $this->calculateSemesterColor($currentConfig, $iSchoolMonth, $schoolYear, $paidAmount, $goalAmount);
                $monthsData[$iSchoolMonth] = $this->buildMonthPayload($currentConfig, $paidAmount, $goalAmount, $color, $realMonth, $realYear);
            }

            // La matriz ahora devuelve la data indexada por la entidad estructural correcta
            $sponsor = $configs->first()->sponsor;
            $matrix[$sponsor['full_name']] = $monthsData;

        }

        return $matrix;
    }

    /**
     * Ejecuta el árbol de decisión del Semáforo
     */
    private function calculateSemesterColor($currentConfig, $iSchoolMonth, $schoolYear, $paidAmount, $goalAmount): string
    {
        $now = now();
        $currentSchoolMonth = asSchoolMonth($now->month);

        // 1. Condición prioritaria: ¿Ya pagó la meta completa del mes?
        if ($paidAmount >= $goalAmount) {
            return 'positive';
        }

        // Calcular offsets y estructuras de bloques
        $offset = asSchoolMonth($currentConfig->startMonthSchool);

        // Evitar divisiones por cero o desbordamientos si el mes evaluado es previo al inicio de la configuración
        if ($iSchoolMonth < $offset) {
            return 'grey';
        }

        $frequency = $currentConfig->frequency;

        // Cantidad de bloques restantes redondeados hacia arriba
        $blocksCount = ceil((12 - $offset + 1) / $frequency);

        // Bloque en el que se encuentra el mes que se está iterando actualmente
        $iMonthBlock = ceil(($iSchoolMonth - $offset + 1) / $frequency);

        // Bloque en el que se encuentra la fecha actual real (sólo si estamos en el mismo año escolar)
        $currentSchoolYearReal = $now->month >= 8 ? $now->year : $now->year - 1;
        if ($schoolYear === $currentSchoolYearReal) {
            $currentBlock = ceil(($currentSchoolMonth - $offset + 1) / $frequency);
        } else {
            // Si consultamos un año escolar pasado, el bloque actual queda desfasado (es mayor)
            $currentBlock = $schoolYear < $currentSchoolYearReal ? 999 : -999;
        }

        $inBlock = ($iMonthBlock == $currentBlock);
        $prevBlocksCount = $iMonthBlock - 1;

        // Mes escolar donde inicia el bloque actual bajo iteración
        $blockStartSchoolMonth = ($prevBlocksCount * $frequency) + $offset;

        // Convertir ese mes escolar de inicio a una instancia Carbon real (Día 1 del mes calendario)
        $blockStartDate = fromSchoolMonth($blockStartSchoolMonth, $schoolYear)->startOfMonth();

        // 2. Condición: Dentro del bloque actual y en los primeros 5 días de gracia
        if ($inBlock && $now <= (clone $blockStartDate)->addDays(5)) {
            return 'yellow';
        }

        // 3. Condición: El mes pertenece a un bloque futuro que aún no entra en vigencia de cobro
        if ($currentBlock < $iMonthBlock) {
            return 'grey';
        }

        // 4. Condición por defecto: Si no pagó, ya pasó el período de gracia o es un bloque vencido
        return 'red-3';
    }

    private function buildMonthPayload($currentConfig, $paid, $goal, $color, $realMonth, $realYear): array
    {
        return [
            'month'              => $realMonth,
            'year'               => $realYear,
            'paid_amount'        => $paid,
            'goal_amount'        => $goal,
            'paid_amount_format' => '$' . number_format($paid, 2),
            'goal_amount_format' => '$' . number_format($goal, 2),
            'color'              => $color,
            'payment_config_id'  => $currentConfig ? $currentConfig->id : null,
            'sponsorship_id'     => $currentConfig ? $currentConfig->sponsorship_id : null,
            'type'               => $currentConfig ? $currentConfig->sponsorship->type : null
        ];
    }
}

if (!function_exists('fromSchoolMonth')) {
    function fromSchoolMonth($schoolMonth, $schoolYear) {
        // Invierte el proceso para obtener el mes real y el año real calendario
        if ($schoolMonth <= 5) { // Agosto a Diciembre
            $realMonth = $schoolMonth + 7;
            $realYear = $schoolYear;
        } else { // Enero a Julio
            $realMonth = $schoolMonth - 5;
            $realYear = $schoolYear + 1;
        }
        return Carbon::createFromDate($realYear, $realMonth, 1)->startOfDay();
    }
}

if (!function_exists('asSchoolMonth')) {
    function asSchoolMonth($month) {
        return $month >= 8 ? $month - 7 : $month + 5;
    }
}
