<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            "id_medical_record" => $this->id_medical_record,
            "candidate_id" => $this->candidate_id,
            "date_medical_record" => $this->date_medical_record,
            "date_soap" => $this->date_soap,
            "hereditary_family_history" => $this->hereditary_family_history,
            "non_pathological_personal_history" => $this->non_pathological_personal_history,
            "perinatal_history" => $this->perinatal_history,
            "andrological_gynecological_obstetric_history" => $this->andrological_gynecological_obstetric_history,
            "medical_history" => $this->medical_history,
            "psychiatric_mental_status" => $this->psychiatric_mental_status,
            "nervous_system" => $this->nervous_system,
            "respiratory_system" => $this->respiratory_system,
            "cardiovascular_system" => $this->cardiovascular_system,
            "digestive_system" => $this->digestive_system,
            "genitourinary_system" => $this->genitourinary_system,
            "musculoskeletal_system" => $this->musculoskeletal_system,
            "endocrine_system" => $this->endocrine_system,
            "sensory_system" => $this->sensory_system,
            "integumentary_system" => $this->integumentary_system,
            "weight" => $this->weight,
            "height" => $this->height,
            "head_circumference" => $this->head_circumference,
            "heart_rate" => $this->heart_rate,
            "respiratory_rate" => $this->respiratory_rate,
            "initial_weight" => $this->initial_weight,
            "weight_age" => $this->weight_age,
            "height_age" => $this->height_age,
            "weight_height" => $this->weight_height,
            "waist_cm" => $this->waist_cm,
            "hip_cm" => $this->hip_cm,
            "chest_cm" => $this->chest_cm,
            "brain_perimeter_cm" => $this->brain_perimeter_cm,
            "brachial_circumference_cm" => $this->brachial_circumference_cm,
            "wrist_circumference_cm" => $this->wrist_circumference_cm,
            "calf_circumference_cm" => $this->calf_circumference_cm,
            "other" => $this->other,
            "imc" => $this->imc,
            "general_inspection" => $this->general_inspection,
            "head" => $this->head,
            "mental_status" => $this->mental_status,
            "hair" => $this->hair,
            "neck" => $this->neck,
            "thorax" => $this->thorax,
            "abdomen" => $this->abdomen,
            "genitalia" => $this->genitalia,
            "anorectal" => $this->anorectal,
            "spine" => $this->spine,
            "upper_lower_limbs" => $this->upper_lower_limbs,
            "peripheral_vascular_system" => $this->peripheral_vascular_system,
            "skin_appendages" => $this->skin_appendages,
            "areas_dryness_excessive_sweating" => $this->areas_dryness_excessive_sweating,
            "diagnostic_impression" => $this->diagnostic_impression,
            "treatment" => $this->treatment,
            "case_analysis" => $this->case_analysis,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "status" => $this->status,
            "appointment_id" => $this->appointment_id,
            "type_id" => $this->type_id,
            "appointment_type" => $this->appointment_type,
        ]);
    }
}