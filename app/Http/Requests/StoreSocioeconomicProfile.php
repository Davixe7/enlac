<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocioeconomicProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Relación
            'candidate_id'                           => ['required', 'exists:candidates,id'],

            // --- SECCIÓN: Datos del Candidato ---
            'facility_tour_notes'                    => ['nullable', 'string', 'max:500'],
            'attends_regular_school'                 => ['nullable', 'boolean'],
            'previously_attended_regular_school'     => ['nullable', 'boolean'],
            'regular_school_grade'                   => ['nullable', 'string', 'max:100'],
            'attends_specialized_school'             => ['nullable', 'boolean'],
            'previously_attended_specialized_school' => ['nullable', 'boolean'],
            'specialized_school_cost'                => ['nullable', 'numeric', 'min:0'],
            'financial_provider_name'                => ['nullable', 'string', 'max:255'],
            'has_formal_employment'                  => ['nullable', 'boolean'],
            'workplace'                              => ['nullable', 'string', 'max:255'],
            'childcare_provider'                     => ['nullable', 'string'],
            'has_more_children'                      => ['nullable', 'boolean'],
            'other_children_details'                 => ['nullable', 'string'],
            'other_children_schools'                 => ['nullable', 'string'],
            'other_children_occupations'             => ['nullable', 'string'],

            // --- SECCIÓN: Datos del Solicitante ---
            'requester_name'                 => ['nullable', 'string', 'max:255'],
            'requester_relationship'         => ['nullable', Rule::in([
                'Madre/Padre', 'Hijo / Hija', 'Hermano(a)', 'Abuelo(a)',
                'Padrastro/Madrastra', 'Hermanastro/Hermanastra', 'Primo(a)', 'Tío(a)'
            ])],
            'requester_age'                  => ['nullable', 'integer', 'min:0', 'max:120'],
            'requester_birth_date'           => ['nullable', 'date'],
            'requester_gender'               => ['nullable', 'string', 'max:50'],
            'requester_marital_status'       => ['nullable', Rule::in([
                'Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión Libre', 'Desconocido'
            ])],
            'requester_phone'                => ['nullable', 'string', 'max:20'],
            'requester_origin'               => ['nullable', 'string', 'max:255'],

            // Domicilio
            'address_street'                 => ['nullable', 'string', 'max:255'],
            'address_ext_num'                => ['nullable', 'string', 'max:20'],
            'address_colony'                 => ['nullable', 'string', 'max:255'],
            'address_zip_code'               => ['nullable', 'string', 'max:10'],
            'address_country'                => ['nullable', 'string', 'max:100'],
            'address_state'                  => ['nullable', 'string', 'max:100'],
            'address_city'                   => ['nullable', 'string', 'max:100'],

            // Salud
            'has_medical_service'            => ['nullable', 'boolean'],
            'medical_institution'            => ['nullable', 'string', 'max:255'],
            'has_specialized_medical_access' => ['nullable', 'boolean'],
            'specialized_medical_type'       => ['nullable', 'string', 'max:255'],

            // --- SECCIÓN: Vivienda ---
            'wall_material'                  => ['nullable', 'string', 'max:255'],
            'roof_material'                  => ['nullable', 'string', 'max:255'],
            'housing_status'                 => ['nullable', 'string', 'max:255'],
            'bathroom_count'                 => ['nullable', 'string', 'max:50'],
            'bedroom_count'                  => ['nullable', 'string', 'max:50'],
            'service_water'                  => ['boolean'],
            'service_drainage'               => ['boolean'],
            'service_electricity'            => ['boolean'],
            'service_phone'                  => ['boolean'],
            'service_internet'               => ['boolean'],
            'has_vehicle'                    => ['nullable', 'boolean'],
            'transport_method'               => ['nullable', 'string', 'max:255'],

            // --- SECCIÓN: Información Adicional ---
            'household_members_count'        => ['nullable', 'integer', 'min:1'],
            'other_disabled_members'         => ['nullable', 'boolean'],
            'disabled_members_type'          => ['nullable', 'string'],
            'disabled_members_count'         => ['nullable', 'integer'],
            'disabled_members_ages'          => ['nullable', 'string'],
            'receives_govt_support'          => ['nullable', 'boolean'],
            'govt_support_institution'       => ['nullable', 'string'],
            'govt_support_amount'            => ['nullable', 'numeric'],

            // --- SECCIÓN: Gastos y Resumen (Decimales según fuente [4]) ---
            'expense_rent'                => ['numeric', 'min:0'],
            'expense_electricity'         => ['numeric', 'min:0'],
            'expense_water'               => ['numeric', 'min:0'],
            'expense_food'                => ['numeric', 'min:0'],
            'expense_special_supplies'    => ['numeric', 'min:0'],
            'expense_phone'               => ['numeric', 'min:0'],
            'expense_school'              => ['numeric', 'min:0'],
            'expense_gas'                 => ['numeric', 'min:0'],
            'expense_gasoline'            => ['numeric', 'min:0'],
            'expense_medical'             => ['numeric', 'min:0'],
            'expense_debts'               => ['numeric', 'min:0'],
            'expense_others'              => ['numeric', 'min:0'],
            'total_expenses'              => ['nullable', 'numeric'],
            'total_income'                => ['nullable', 'numeric'],
            'income_expense_difference'   => ['nullable', 'numeric'],
            'solvency_notes'              => ['nullable', 'string'],
            'economic_level'              => ['nullable', 'string'],
            'monthly_contribution_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
