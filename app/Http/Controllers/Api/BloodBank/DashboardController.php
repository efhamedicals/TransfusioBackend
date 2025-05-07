<?php

namespace App\Http\Controllers\Api\BloodBank;

use App\Http\Controllers\Controller;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Http\Resources\Transfusion as TransfusionResource;
use App\Models\BloodBank;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Transfusion;
use App\Models\StockBloodBank;
use Illuminate\Http\Request;
use App\Http\Resources\StockBloodBank as StockBloodBankResource;
use App\Http\Resources\BloodBank as BloodBankResource;
use App\Models\BloodBag;
use App\Models\TypeProductBlood;
use App\Http\Resources\BloodBag as BloodBagResource;
use App\Models\TypeBlood;

class DashboardController extends Controller
{
    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function homeDatas(Request $request, $idBloodBank)
    {
        $args = array();
        $args['error'] = false;
        try {
            $bloodBank = BloodBank::where(['id' => $idBloodBank])->first();
            $args["count_patients"] = Patient::where('hospital_id', '=', $bloodBank->hospital_id)->get()->count();
            $args["count_prescriptions"] = Prescription::where('hospital_id', '=', $bloodBank->hospital_id)->get()->count();
            $args["count_demandes"] = Prescription::where('hospital_id', '=', $bloodBank->hospital_id)->where('status', '=', 2)->get()->count();
            $args["count_livraisons"] = Prescription::where('hospital_id', '=', $bloodBank->hospital_id)->where('status', '=', 4)->get()->count();
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }

        return response()->json(
            $args
        );
    }
    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function stocks(Request $request, $idBloodBank)
    {
        return response()->json(
            StockBloodBankResource::collection(StockBloodBank::where(['blood_bank_id' => $idBloodBank])->get())
        );
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function details(Request $request, $idBloodBank)
    {
        return response()->json(
            new BloodBankResource(BloodBank::where(['id' => $idBloodBank])->first())
        );
    }

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function stocksProducts(Request $request, $idBloodBank, $idTypeProduct)
    {
        $args = array();
        $args['bloods'] = BloodBagResource::collection(BloodBag::where(['blood_bank_id' => $idBloodBank, 'type_product_blood_id' => $idTypeProduct])->get());
        $args['product'] = TypeProductBlood::where(['id' => $idTypeProduct])->first();
        return response()->json(
            $args
        );
    }
    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function cashbox(Request $request, $idBloodBank)
    {
        $bloodBank = BloodBank::where(['id' => $idBloodBank])->first();
        return response()->json(
            PrescriptionResource::collection(Prescription::where(['hospital_id' => $bloodBank->hospital_id])->where('status', '>=', 5)->orderBy('created_at', 'DESC')->get())
        );
    }
    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function prescriptions(Request $request, $idBloodBank)
    {
        $bloodBank = BloodBank::where(['id' => $idBloodBank])->first();
        return response()->json(
            PrescriptionResource::collection(Prescription::where(['hospital_id' => $bloodBank->hospital_id])->where('status', '!=', 1)->orderBy('created_at', 'DESC')->get())
        );
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function hemovigilances(Request $request, $idBloodBank)
    {
        $bloodBank = BloodBank::where(['id' => $idBloodBank])->first();
        return response()->json(
            TransfusionResource::collection(Transfusion::where(['hemo_file' => 1])->orderBy('created_at', 'DESC')->get())
        );
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function prescriptionsWrong(Request $request, $idBloodBank)
    {
        $bloodBank = BloodBank::where(['id' => $idBloodBank])->first();
        return response()->json(
            PrescriptionResource::collection(Prescription::where(['hospital_id' => $bloodBank->hospital_id])->where('status', '!=', 1)->orderBy('created_at', 'DESC')->get())
        );
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function detailsPrescription(Request $request, $idPrescription)
    {

        return response()->json(
            new  PrescriptionResource(Prescription::where(['id' => $idPrescription])->where('status', '!=', 1)->first())
        );
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function confirmAvailability(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 5,
            ]);
            $args['message'] = "Disponibilité de la prescription confirmée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function confirmAvailabilityPartial(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 4,
            ]);
            $args['message'] = "Disponibilité de la prescription confirmée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function cancelAvailabilityNow(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 3,
            ]);
            $args['message'] = "Non Disponibilité de la prescription confirmée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function cancelAvailability(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {

            Prescription::where(['id' => $idPrescription])->update([
                'status' => 0,
            ]);
            $args['message'] = "Non Disponibilité de la prescription confirmée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api BloodBank Dashboard
     *
     */
    public function confirmPayment(Request $request, $idPrescription)
    {
        $args = array();
        $args['error'] = false;
        try {
            $prescription = Prescription::where(['id' => $idPrescription])->first();
            if (BloodBag::where([
                'type_product_blood_id' => $prescription->products[0]->type_product_blood_id,
                'format' => $prescription->products[0]->format,
                'type_blood_id' => TypeBlood::where(['name' => $prescription->patient->blood_type . $prescription->patient->rhesus])->first()->id,
            ])
                ->first()
            ) {
                $bloodBag =   BloodBag::where([
                    'type_product_blood_id' => $prescription->products[0]->type_product_blood_id,
                    'format' => $prescription->products[0]->format,
                    'type_blood_id' => TypeBlood::where(['name' => $prescription->patient->blood_type . $prescription->patient->rhesus])->first()->id,
                ])->first();
                if ($bloodBag->date_expiration >= Date('Y-m-d')) {
                    $bloodBag->update([
                        'blood_bank_id' => BloodBank::where(['id' => $prescription->hospital_id])->first()->id,
                    ]);

                    Prescription::where(['id' => $idPrescription])->update([
                        'status' => 7,
                        'blood_bag_id' => $bloodBag->id,
                    ]);
                    $args['message'] = "Prescription payée avec succès!";
                } else {
                    $args['error'] = true;
                    $args['message'] = "Le produit est périmé!";
                }
            } else {
                $args['error'] = true;
                $args['message'] = "Le produit n'est pas disponible";
            }
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
}
