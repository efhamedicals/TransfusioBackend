<?php

namespace App\Http\Controllers\Api\BloodCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Http\Resources\Transfusion as TransfusionResource;
use App\Http\Resources\BloodBank as BloodBankResource;
use App\Models\BloodBank;
use App\Http\Resources\StockBloodCenter as StockBloodCenterResource;
use App\Models\RenewStockCenter;
use App\Models\RenewStockCenterItem;
use App\Models\StockBloodCenter;
use App\Models\TypeBlood;
use App\Models\BloodBag;
use App\Models\TypeProductBlood;
use App\Http\Resources\BloodBag as BloodBagResource;
use App\Models\BloodCenter;
use App\Models\Geolocation;
use App\Models\Hospital;
use App\Models\Prescription;
use App\Models\Transfusion;

class DashboardController extends Controller
{
    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function bloodbanks(Request $request)
    {
        return response()->json(
            BloodBankResource::collection(BloodBank::where(['status' => 1])->get())
        );
    }

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function stocks(Request $request, $idBloodCenter)
    {
        return response()->json(
            StockBloodCenterResource::collection(StockBloodCenter::where(['blood_center_id' => $idBloodCenter])->get())
        );
    }

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function stocksProducts(Request $request, $idBloodCenter, $idTypeProduct)
    {
        $args = array();
        $args['bloods'] = BloodBagResource::collection(BloodBag::where(['blood_center_id' => $idBloodCenter, 'type_product_blood_id' => $idTypeProduct, 'blood_bank_id' => null])->get());
        $args['product'] = TypeProductBlood::where(['id' => $idTypeProduct])->first();
        return response()->json(
            $args
        );
    }


    public function addBloodBank(Request $request, $idBloodCenter)
    {
        $args = array();
        $args['error'] = false;
        $name = $request->name;
        $shortName = $request->shortName;
        $email = $request->email;
        $phone = $request->phone;
        $address = $request->address;
        $city = $request->city;
        $latitude = $request->latitude;
        $longitude = $request->longitude;



        try {
            $geolocation = Geolocation::create([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'name' => $address . " " . $city
            ]);
            $hospital =  Hospital::create([
                'name' => $name,
                'short_name' => $shortName,
                'email' => $email,
                'phone' => $phone,
                'avatar' => "/avatars/default.jpeg",
                'status' => 1,
                'geolocation_id' => $geolocation->id
            ]);

            BloodBank::create([
                'hospital_id' => $hospital->id,
                'status' => 1
            ]);

            $args['message'] = "Stock renouvelé avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }


    public function addStock(Request $request, $idBloodCenter)
    {
        $args = array();
        $args['error'] = false;
        $datas = $request->datas;
        $type_product_blood_id = $request->type_product_id;



        try {
            $bloodsDecoded = json_decode($datas);

            foreach ($bloodsDecoded as $blood) {
                $format = 1;
                $reference = "";
                $dateExpiration = "";
                $type = "";
                $price = 0;
                foreach ($blood as $key => $value) {
                    if ($key == "format") {
                        $format = $value;
                    }
                    if ($key == "reference") {
                        $reference = $value;
                    }
                    if ($key == "dateExpiration") {
                        $dateExpiration = $value;
                    }
                    if ($key == "typeBlood") {
                        $type = $value;
                    }
                    if ($key == "price") {
                        $price = intval($value);
                    }
                }

                BloodBag::create([
                    'type_blood_id' => ($type != null) ? TypeBlood::where(['name' => $type])->first()->id : null,
                    'type_product_blood_id' => $type_product_blood_id,
                    'reference' => $reference,
                    'price' => $price,
                    'date_expiration' => explode("T", $dateExpiration)[0],
                    'format' => ($type != null) ?  $format : 0,
                    'status' => 1,
                    'blood_center_id' => $idBloodCenter
                ]);
            }


            $args['message'] = "Stock renouvelé avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }


    public function assignBlood(Request $request, $idBloodCenter)
    {
        $args = array();
        $args['error'] = false;
        $blood_bank_id = $request->blood_bank_id;
        $blood_id = $request->blood_id;



        try {

            BloodBag::where(['id' => $blood_id])->update([
                'blood_bank_id' => $blood_bank_id,
            ]);


            $args['message'] = "Stock renouvelé avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }


    /* public function addOldStock(Request $request, $idBloodCenter)
    {
        $args = array();
        $args['error'] = false;
        $name = $request->name;
        // $a_plus = $request->a_plus;
        // $a_moins = $request->a_moins;
        // $b_plus = $request->b_plus;
        // $b_moins = $request->b_moins;
        // $ab_plus = $request->ab_plus;
        // $ab_moins = $request->ab_moins;
        // $o_plus = $request->o_plus;
        // $o_moins = $request->o_moins;

        try {
            $renew = RenewStockCenter::create([
                'name' => $name,
                'reference' => getRamdomText(10),
                'blood_center_id' => $idBloodCenter
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'A+'])->first()->id,
                'quantity' => $a_plus,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'A-'])->first()->id,
                'quantity' => $a_moins,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'B+'])->first()->id,
                'quantity' => $b_plus,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'B+'])->first()->id,
                'quantity' => $b_moins,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'AB+'])->first()->id,
                'quantity' => $ab_plus,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'AB-'])->first()->id,
                'quantity' => $ab_moins,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'O+'])->first()->id,
                'quantity' => $o_plus,
                'renew_stock_center_id' => $renew->id
            ]);
            RenewStockCenterItem::create([
                'type_blood_id' => TypeBlood::where(['name' => 'O-'])->first()->id,
                'quantity' => $o_moins,
                'renew_stock_center_id' => $renew->id
            ]);

            $args['message'] = "Stock renouvelé avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }*/

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function hemovigilances(Request $request, $idBloodCenter)
    {
        $bloodCenter = BloodCenter::where(['id' => $idBloodCenter])->first();
        return response()->json(
            TransfusionResource::collection(Transfusion::where(['hemo_file' => 1])->orderBy('created_at', 'DESC')->get())
        );
    }

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function prescriptionsWrong(Request $request, $idBloodCenter)
    {
        $bloodCenter = BloodCenter::where(['id' => $idBloodCenter])->first();
        return response()->json(
            PrescriptionResource::collection(Prescription::where('status', '!=', 1)->orderBy('created_at', 'DESC')->get())
        );
    }

    /**
     * @group  Api BloodCenter Dashboard
     *
     */
    public function detailsPrescription(Request $request, $idPrescription)
    {

        return response()->json(
            new  PrescriptionResource(Prescription::where(['id' => $idPrescription])->where('status', '!=', 1)->first())
        );
    }
}
