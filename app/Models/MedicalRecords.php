<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MedicalRecords extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'medical_records';

    // Si tu clave primaria no se llama "id"
    protected $primaryKey = 'id_medical_record';

    // Campos que permites que se llenen masivamente (protección contra vulnerabilidades)
    protected $fillable = ['candidate_id',"date_medical_record",
            "hereditary_family_history",
            "non_pathological_personal_history",
            "perinatal_history",
            "andrological_gynecological_obstetric_history",
            "medical_history",
            "psychiatric_mental_status",
            "nervous_system",
            "respiratory_system",
            "cardiovascular_system",
            "digestive_system",
            "genitourinary_system",
            "musculoskeletal_system",
            "endocrine_system",
            "sensory_system",
            "integumentary_system",
            "weight",
            "height",
            "head_circumference",
            "heart_rate",
            "initial_weight",
            "weight_age",
            "height_age",
            "weight_height",
            "waist_cm",
            "hip_cm",
            "chest_cm",
            "brain_perimeter_cm",
            "brachial_circumference_cm",
            "wrist_circumference_cm",
            "calf_circumference_cm",
            "other",
            "imc",
            "respiratory_rate",
            "temperature",
            "general_inspection",
            "head",
            "mental_status",
            "hair",
            "neck",
            "thorax",
            "abdomen",
            "genitalia",
            "anorectal",
            "spine",
            "upper_lower_limbs",
            "peripheral_vascular_system",
            "skin_appendages",
            "areas_dryness_excessive_sweating",
            "diagnostic_impression",
            "treatment",
            "case_analysis",
            "created_at",
            "updated_at",
            "subjective",
            "objective",
            "assessment",
            "plan",
            "date_soap",
            "appointment_id",
            "type_id",
            "appointment_type",
            "gender"];

    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'date_medical_record' => 'datetime:d/m/Y',
    ];
}