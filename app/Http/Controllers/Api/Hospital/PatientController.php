<?php

namespace App\Http\Controllers\Api\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Patient as PatientResource;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Http\Resources\Antecedent as AntecedentResource;
use App\Models\Antecedent;
use App\Models\AntecedentProduct;
use App\Models\AntecedentReaction;
use App\Models\Constant;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\TypeProductBlood;

class PatientController extends Controller
{
    /**
     * @group  Api Hospital Patient
     *
     */
    public function all(Request $request, $idHospital)
    {
        return response()->json(
            PatientResource::collection(Patient::where('hospital_id', '=', $idHospital)->orderBy("last_name", 'ASC')->get())
        );
    }
    /**
     * @group  Api Hospital Patient
     *
     */
    public function add(Request $request)
    {
        $args = array();
        $args['error'] = false;
        $last_name = $request->last_name;
        $first_name = $request->first_name;
        $married_name = $request->married_name;
        $gender = $request->gender;
        $birth = formatDate2($request->birth);
        $academic_level = $request->academic_level;
        $blood_type = $request->blood_type;
        $rhesus = $request->rhesus;
        $email = $request->email;
        $phone = $request->phone;
        $photo = $request->photo;
        $cni = $request->cni;
        $count_pregnancies = $request->count_pregnancies;
        $hospital_id = $request->hospital_id;
        try {
            if (!Patient::where(['last_name' => $last_name, 'first_name' => $first_name])->first()) {

                if ($photo != "" && $photo != null) {
                    $reference = getRamdomText(10);
                    $photo = "/patients/" . $last_name . $reference . ".jpg";
                    //$destinationPath = public_path('/patients');
                    $destinationPath = "/home/www/safebloodapi.kofcorporation.com/patients";
                    $ImagePath = $destinationPath . "/" . $last_name . $reference . ".jpg";
                    file_put_contents($ImagePath, base64_decode($photo));
                }

                if ($cni != "" && $cni != null) {
                    $reference = getRamdomText(10);
                    $cni = "/patients/" . $last_name . $reference . ".jpg";
                    //$destinationPath = public_path('/patients');
                    $destinationPath = "/home/www/safebloodapi.kofcorporation.com/patients";
                    $ImagePath = $destinationPath . "/" . $last_name . $reference . ".jpg";
                    file_put_contents($ImagePath, base64_decode($cni));
                }

                $Patient = Patient::create([
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'married_name' => $married_name,
                    'gender' => $gender,
                    'birth' => $birth,
                    'academic_level' => $academic_level,
                    'blood_type' => $blood_type,
                    'rhesus' => $rhesus,
                    'email' => $email,
                    'phone' => $phone,
                    'photo' => $photo,
                    'cni' => $cni,
                    'count_pregnancies' => $count_pregnancies,
                    'hospital_id' => $hospital_id,
                    'status' => 1
                ]);
                $args['Patient'] = new PatientResource($Patient);
                $args['message'] = "Patient crée avec succès!";
            } else {
                $args['message'] = "Un patient existe déjà avec les mêmes informations.";
                $args['error'] = true;
            }
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api Hospital Patient
     *
     */
    public function details(Request $request, $idPatient)
    {
        $args = array();
        $args["patient"] = Patient::where('id', '=', $idPatient)->get();
        $args["antecedents"] = AntecedentResource::collection(Antecedent::where('patient_id', '=', $idPatient)->get());
        $args["constants"] = Constant::where('patient_id', '=', $idPatient)->get();
        $args["prescriptions"] = PrescriptionResource::collection(Prescription::where('patient_id', '=', $idPatient)->where('status', '=', 8)->orderBy("created_at", 'DESC')->get());
        return response()->json(
            $args
        );
    }

    /**
     * @group  Api Hospital Patient
     *
     */
    public function addAntecedent(Request $request, $idPatient)
    {
        $args = array();
        $args['error'] = false;
        $clinic = ($request->clinic == "Grossesse") ? 1 : 2;
        $result_treatment = ($request->result_treatment == "Bon") ? 1 : 2;
        $treatments = $request->treatments;
        $date_antecedent = str_replace("00:00", '', $request->date_antecedent);
        $results_treatments = $request->results_treatments;
        $products = $request->products;
        $reactions = $request->reactions;
        try {

            $antecedent =  Antecedent::create([
                'clinic' => $clinic,
                'result_treatment' => $result_treatment,
                'treatments' => $treatments,
                'date_antecedent' => $date_antecedent,
                'results_treatments' => $results_treatments,
                'patient_id' => $idPatient,
                'status' => 1
            ]);
            $productsDecoded = json_decode($products);
            $reactionsDecoded = json_decode($reactions);

            foreach ($productsDecoded as $product) {
                // return ($product);
                $format = "";
                $groupe = "";
                $rhesus = "";
                $type = 0;
                foreach ($product as $key => $value) {
                    if ($key == "format") {
                        $format = $value;
                    }
                    if ($key == "groupe") {
                        $groupe = $value;
                    }
                    if ($key == "rhesus") {
                        $rhesus = $value;
                    }
                    if ($key == "type") {
                        $type = $value;
                    }
                }
                AntecedentProduct::create([
                    'format' => ($format == "Adulte") ? 1 : 2,
                    'blood_type' => $groupe,
                    'rhesus' => $rhesus,
                    'type_product_blood_id' => TypeProductBlood::where('name', '=', $type)->first()->id,
                    'antecedent_id' => $antecedent->id
                ]);
            }


            foreach ($reactionsDecoded as $reaction) {
                AntecedentReaction::create([
                    'name' => $reaction,
                    'antecedent_id' => $antecedent->id
                ]);
            }

            $args['message'] = "Antécédant crée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api Hospital Patient
     *
     */
    public function addConstant(Request $request, $idPatient)
    {
        $args = array();
        $args['error'] = false;
        $rate = $request->rate;
        $hall_pavilion = $request->hall_pavilion;
        $comments = $request->comments;
        try {

            Constant::create([
                'comments' => $comments,
                'hall_pavilion' => $hall_pavilion,
                'rate' => $rate,
                'patient_id' => $idPatient,

            ]);

            $args['message'] = "Constante ajoutée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
}
