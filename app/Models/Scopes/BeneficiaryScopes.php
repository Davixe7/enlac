<?php

namespace App\Models\Scopes;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait BeneficiaryScopes
{
    public function scopeBirthDate(Builder $query, $birthDate): Builder
    {
        return $birthDate ? $query->where('birth_date', $birthDate) : $query;
    }

    public function scopeName(Builder $query, $name): Builder
    {
        if( !$name ){ return $query; }
        return
            $query->where(function (Builder $q) use ($name) {
                $q->where('first_name', 'like', '%' . $name . '%')
                    ->orWhere('middle_name', 'like', '%' . $name . '%')
                    ->orWhere('last_name', 'like', '%' . $name . '%');
            });
    }

    public function scopePending(Builder $query)
    {
        return $query->where('candidate_status_id', 1);
    }

    public function scopeBeneficiaries(Builder $query)
    {
        return $query->whereNotIn('candidate_status_id', [1, 2, 7, 8, 9]);
    }

    public function scopeEquinetherapyActivePlan($query)
    {
        return $query->whereHas('groups', function ($group) {
            $group->whereHas('plans', fn ($plan) => $plan->where('category_id', 5));
        });
    }

    public function scopeEvaluationBetween(Builder $query, $startDate, $endDate): Builder
    {
        if (!$startDate || !$endDate) {
            return $query;
        }

        $endDate = Carbon::parse($endDate)->endOfDay();

        // Subquery to find most recent evaluation schedule ID 
        // for each candidate within the date range
        $mostRecentScheduleIds = Appointment::query()
            ->where('type_id', 0)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('candidate_id', DB::raw('MAX(id) as most_recent_id'))
            ->groupBy('candidate_id');

        return $query->joinSub($mostRecentScheduleIds, 'most_recent_schedules', function ($join) {
            $join->on('candidates.id', '=', 'most_recent_schedules.candidate_id');
        });
    }

    public function scopeBasic(Builder $query, $fields = [])
    {
        $default = ['id', 'first_name', 'middle_name', 'last_name', 'candidate_status_id'];
        $fields = array_merge($default, $fields);
        $query->select($fields);
        return $query;
    }

    public function scopeSearchByName($query, $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            // Opción A: Buscar palabra por palabra en las 3 columnas
            $q->whereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%$search%"])
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
        });
    }
}
