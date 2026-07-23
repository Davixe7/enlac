<?php

namespace App\Http\Controllers;


use App\Models\Candidate;
use App\Models\MedicalRecords;
use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class MedicalRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = MedicalRecords::all();
        return MedicalRecordResource::collection($records);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "candidate_id" => "nullable|integer",
            "date_medical_record" => "nullable|date",
            "hereditary_family_history" => "nullable|string",
            "non_pathological_personal_history" => "nullable|string",
            "perinatal_history" => "nullable|string",
            "andrological_gynecological_obstetric_history" => "nullable|string",
            "medical_history" => "nullable|string",
            "psychiatric_mental_status" => "nullable|string",
            "nervous_system" => "nullable|string",
            "respiratory_system" => "nullable|string",
            "cardiovascular_system" => "nullable|string",
            "digestive_system" => "nullable|string",
            "genitourinary_system" => "nullable|string",
            "musculoskeletal_system" => "nullable|string",
            "endocrine_system" => "nullable|string",
            "sensory_system" => "nullable|string",
            "integumentary_system" => "nullable|string",
            "weight" => "nullable|string",
            "height" => "nullable|string",
            "head_circumference" => "nullable|string",
            "heart_rate" => "nullable|string",
            "respiratory_rate" => "nullable|string",
            "initial_weight" => "nullable|string",
            "weight_age" => "nullable|string",
            "height_age" => "nullable|string",
            "weight_height" => "nullable|string",
            "waist_cm" => "nullable|string",
            "hip_cm" => "nullable|string",
            "chest_cm" => "nullable|string",
            "brain_perimeter_cm" => "nullable|string",
            "brachial_circumference_cm" => "nullable|string",
            "wrist_circumference_cm" => "nullable|string",
            "calf_circumference_cm" => "nullable|string",
            "other" => "nullable|string",
            "imc" => "nullable|string",
            "temperature" => "nullable|string",
            "general_inspection" => "nullable|string",
            "head" => "nullable|string",
            "mental_status" => "nullable|string",
            "hair" => "nullable|string",
            "neck" => "nullable|string",
            "thorax" => "nullable|string",
            "abdomen" => "nullable|string",
            "genitalia" => "nullable|string",
            "anorectal" => "nullable|string",
            "spine" => "nullable|string",
            "upper_lower_limbs" => "nullable|string",
            "peripheral_vascular_system" => "nullable|string",
            "skin_appendages" => "nullable|string",
            "areas_dryness_excessive_sweating" => "nullable|string",
            "diagnostic_impression" => "nullable|string",
            "treatment" => "nullable|string",
            "case_analysis" => "nullable|string",
            "created_at" => "nullable|date",
            "updated_at" => "nullable|date",
            "subjective" => "nullable|string",
            "objective" => "nullable|string",
            "assessment" => "nullable|string",
            "plan" => "nullable|string",
            "date_soap" => "nullable|date",
            "appointment_id" => "required|exists:appointments,id",
            "type_id" => "nullable|integer",
            "appointment_type" => "nullable|integer",
            "gender" => "nullable|string",
        ]);
        $medicalRecord = MedicalRecords::create($validated);

        return new MedicalRecordResource($medicalRecord);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $candidate_id)
    {
        $medicalRecord = MedicalRecords::where('candidate_id', $candidate_id)
            ->where('status', 1)
            ->first();
        return new MedicalRecordResource($medicalRecord);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id_medical_record)
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id_medical_record)
    {
        $validated = $request->validate([
            "candidate_id" => "nullable|integer",
            "date_medical_record" => "nullable|date",
            "hereditary_family_history" => "nullable|string",
            "non_pathological_personal_history" => "nullable|string",
            "perinatal_history" => "nullable|string",
            "andrological_gynecological_obstetric_history" => "nullable|string",
            "medical_history" => "nullable|string",
            "psychiatric_mental_status" => "nullable|string",
            "nervous_system" => "nullable|string",
            "respiratory_system" => "nullable|string",
            "cardiovascular_system" => "nullable|string",
            "digestive_system" => "nullable|string",
            "genitourinary_system" => "nullable|string",
            "musculoskeletal_system" => "nullable|string",
            "endocrine_system" => "nullable|string",
            "sensory_system" => "nullable|string",
            "integumentary_system" => "nullable|string",
            "weight" => "nullable|string",
            "height" => "nullable|string",
            "head_circumference" => "nullable|string",
            "heart_rate" => "nullable|string",
            "initial_weight" => "nullable|string",
            "weight_age" => "nullable|string",
            "height_age" => "nullable|string",
            "weight_height" => "nullable|string",
            "waist_cm" => "nullable|string",
            "hip_cm" => "nullable|string",
            "chest_cm" => "nullable|string",
            "brain_perimeter_cm" => "nullable|string",
            "brachial_circumference_cm" => "nullable|string",
            "wrist_circumference_cm" => "nullable|string",
            "calf_circumference_cm" => "nullable|string",
            "other" => "nullable|string",
            "imc" => "nullable|string",
            "respiratory_rate" => "nullable|string",
            "temperature" => "nullable|string",
            "general_inspection" => "nullable|string",
            "head" => "nullable|string",
            "mental_status" => "nullable|string",
            "hair" => "nullable|string",
            "neck" => "nullable|string",
            "thorax" => "nullable|string",
            "abdomen" => "nullable|string",
            "genitalia" => "nullable|string",
            "anorectal" => "nullable|string",
            "spine" => "nullable|string",
            "upper_lower_limbs" => "nullable|string",
            "peripheral_vascular_system" => "nullable|string",
            "skin_appendages" => "nullable|string",
            "areas_dryness_excessive_sweating" => "nullable|string",
            "diagnostic_impression" => "nullable|string",
            "treatment" => "nullable|string",
            "case_analysis" => "nullable|string",
            "created_at" => "nullable|date",
            "updated_at" => "nullable|date",
            "subjective" => "nullable|string",
            "objective" => "nullable|string",
            "assessment" => "nullable|string",
            "plan" => "nullable|string",
            "date_soap" => "nullable|date",
            "appointment_id" => "required|exists:appointments,id",
            "type_id" => "nullable|integer",
            "appointment_type" => "nullable|integer",
            "gender" => "nullable|string"
        ]);

        $medicalRecord = MedicalRecords::findOrFail($id_medical_record);
        $medicalRecord->update($validated);
        return response()->json(['message' => 'exito.', "data" => $medicalRecord], 200);
    }

    public function uploadMedia(Request $request, int $medical_record_id){
        $collectionName = 'medicalFile_' . $medical_record_id;
        $medicalRecord = MedicalRecords::findOrFail($medical_record_id);
        // $medicalRecord->clearMediaCollection($collectionName);

        // Permitir múltiples archivos
        if ($request->hasFile('upload')) {
            $files = $request->file('upload');
            foreach ((array)$files as $file) {
                if (is_null($request->detail) || $request->detail == 'null') {
                    $medicalRecord
                        ->addMedia($file)
                        ->toMediaCollection($collectionName);
                } else {
                    $medicalRecord
                        ->addMedia($file)
                        ->preservingOriginal()
                        ->withCustomProperties(['detail' => $request->detail])
                        ->toMediaCollection($collectionName);
                }
            }
        }

        $media = $medicalRecord->getMedia($collectionName);
        return MediaResource::collection($media);
    }

    public function deleteMedia(int $medical_record_id, int $file_id){
        $medicalRecord = MedicalRecords::findOrFail($medical_record_id);
        $mediaItem = $medicalRecord->getMedia('medicalFile_' . $medical_record_id)->where('id', $file_id)->first();

        if (!$mediaItem) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $mediaItem->delete();
        return response()->json(['message' => 'File deleted successfully.']);
    }
    public function showMedicalFiles(int $medical_record_id)
    {
        $medicalRecord = MedicalRecords::with('media')->findOrFail($medical_record_id);
        $media = $medicalRecord->getMedia('medicalFile_' . $medical_record_id);
        return MediaResource::collection($media);
    }
    // Métodos para SOAP
    public function uploadSoapMedia(Request $request, int $medical_record_id){
        $collectionName = 'soapFile_' . $medical_record_id;
        $medicalRecord = MedicalRecords::findOrFail($medical_record_id);
        // $medicalRecord->clearMediaCollection($collectionName);

        // Permitir múltiples archivos
        if ($request->hasFile('upload')) {
            $files = $request->file('upload');
            foreach ((array)$files as $file) {
                if (is_null($request->detail) || $request->detail == 'null') {
                    $medicalRecord
                        ->addMedia($file)
                        ->toMediaCollection($collectionName);
                } else {
                    $medicalRecord
                        ->addMedia($file)
                        ->preservingOriginal()
                        ->withCustomProperties(['detail' => $request->detail])
                        ->toMediaCollection($collectionName);
                }
            }
        }

        $media = $medicalRecord->getMedia($collectionName);
        return MediaResource::collection($media);
    }

    public function deleteSoapMedia(int $medical_record_id, int $file_id){
        $medicalRecord = MedicalRecords::findOrFail($medical_record_id);
        $mediaItem = $medicalRecord->getMedia('soapFile_' . $medical_record_id)->where('id', $file_id)->first();

        if (!$mediaItem) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $mediaItem->delete();
        return response()->json(['message' => 'File deleted successfully.']);
    }
    public function showSoapFiles(int $medical_record_id)
    {
        $soap = MedicalRecords::with('media')->findOrFail($medical_record_id);
        $media = $soap->getMedia('soapFile_' . $medical_record_id);
        return MediaResource::collection($media);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id_medical_record)
    {
        $medicalRecord = MedicalRecords::findOrFail($id_medical_record);
        $medicalRecord->delete();
    
        return response()->json(['message' => 'Medical record deleted successfully.']);
    }
}