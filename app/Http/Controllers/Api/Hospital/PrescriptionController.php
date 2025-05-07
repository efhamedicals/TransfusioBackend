<?php

namespace App\Http\Controllers\Api\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PrescriptionProduct;
use App\Models\TypeProductBlood;
use Illuminate\Http\Request;
use App\Http\Resources\Prescription as PrescriptionResource;

class PrescriptionController extends Controller
{

    /**
     * @group  Api Hospital Prescription
     *
     */
    public function all(Request $request, $idHospital)
    {
        return response()->json(
            PrescriptionResource::collection(Prescription::where('hospital_id', '=', $idHospital)->orderBy("created_at", 'DESC')->get())
        );
    }


    /**
     * @group  Api Hospital Prescription
     *
     */
    public function add(Request $request, $idHospital)
    {
        $args = array();
        $args['error'] = false;
        $patient_id = $request->patient_id;
        $user_id = $request->user_id;
        $rai = $request->rai;
        $count_bags = $request->count_bags;
        $priority = $request->priority;
        $format = $request->format;
        $is_chirurgical = $request->is_chirurgical;
        $is_replace = $request->is_replace;
        $justifications = $request->justifications;
        $indications = $request->indications;
        $instructions = $request->instructions;
        $type_blood = $request->type_blood;

        if ($format == "Adulte") {
            $formatVerified = 1;
        } else {
            $formatVerified = 2;
        }

        if ($priority == "Urgence vitale immédiate") {
            $priorityVerified = 1;
        } else if ($priority == 'Urgence vitale') {
            $priorityVerified = 2;
        } else if ($priority == 'Urgence relative') {
            $priorityVerified = 3;
        } else {
            $priorityVerified = 4;
        }
        try {

            $reference = "PRES-" . getRamdomInt(5);
            $prescription =  Prescription::create([
                'hospital_id' => $idHospital,
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'reference' => $reference,
                'rai' => $rai,
                'status' => 1
            ]);

            PrescriptionProduct::create([
                'count_bags' => $count_bags,
                'priority' => $priorityVerified,
                'format' => $formatVerified,
                'is_chirurgical' => $is_chirurgical,
                'is_replace' => $is_replace,
                'justifications' => $justifications,
                'indications' => $indications,
                'instructions' => $instructions,
                'type_product_blood_id' => TypeProductBlood::where('name', '=', $type_blood)->first()->id,
                'prescription_id' => $prescription->id
            ]);

            $args['message'] = "Prescription ajoutée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api Hospital Prescription
     *
     */
    public function checkAvailability(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 2,
            ]);
            $args['message'] = "Vérification envoyée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Prescription
     *
     */
    public function confirmReception(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 8
            ]);
            $args['message'] = "Reception confirmée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
}
