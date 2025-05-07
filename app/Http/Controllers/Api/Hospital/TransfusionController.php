<?php

namespace App\Http\Controllers\Api\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Transfusion;
use App\Models\BloodBag;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Resources\Transfusion as TransfusionResource;
use App\Models\TransfusionConstant;
use App\Models\TransfusionReaction;

class TransfusionController extends Controller
{

    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function all(Request $request, $idHospital)
    {
        return response()->json(
            TransfusionResource::collection(Transfusion::where('hospital_id', '=', $idHospital)->join('prescriptions', 'prescriptions.id', '=', 'transfusions.prescription_id')
                ->orderBy("created_at", 'DESC')->get(['transfusions.*']))
        );
    }

    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function details(Request $request, $idTransfusion)
    {
        return response()->json(
            new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first())
        );
    }


    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function add(Request $request, $idHospital)
    {
        $args = array();
        $args['error'] = false;
        $blood_bag_id = $request->blood_bag_id;
        $img = $request->img;

        $bloodBag = BloodBag::where('reference', '=', $blood_bag_id)->first();

        $prescription = Prescription::where('blood_bag_id', '=', $bloodBag->id)->first();

        try {
            $image = "";
            if ($img != "" && $img != null) {
                $reference = getRamdomText(10);
                $image = "/patients/" . $blood_bag_id . $reference . ".jpg";
                //$destinationPath = public_path('/patients');
                $destinationPath = "/home/www/safebloodapi.kofcorporation.com/patients";
                $ImagePath = $destinationPath . "/" . $blood_bag_id . $reference . ".jpg";
                file_put_contents($ImagePath, base64_decode($image));
            }


            $transfusion =  Transfusion::create([
                'prescription_id' => $prescription->id,
                'image' => $image,
                'status' => 0
            ]);
            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion ajoutée avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }

    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function update(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;
        $quantity = $request->quantity;
        $rythm = $request->rythm;

        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        try {

            $transfusion->quantity =  $quantity;
            $transfusion->rythm =  $rythm;
            $transfusion->save();

            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());

            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function start(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;


        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        try {

            $transfusion->status =  1;
            $transfusion->start_transfusion =  date('Y-m-d H:i:s');
            $transfusion->save();
            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());
            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function end(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;


        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        try {

            $transfusion->status =  2;
            $transfusion->end_transfusion =  date('Y-m-d H:i:s');
            $transfusion->save();
            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());
            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function addConstants(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;
        $constants = json_decode($request->constants);
        $type = intval($request->type) + 1;

        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        try {

            foreach ($constants as $key => $value) {
                if (TransfusionConstant::where(["transfusion_id" => $transfusion->id, "name" => $key, "type" => $type])->first()) {
                    $type = $type + 1;
                }
                TransfusionConstant::create([
                    'transfusion_id' => $transfusion->id,
                    'name' => $key,
                    'value' => $value,
                    'type' => $type,
                    'status' => 1
                ]);
            }

            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());

            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function generateHemo(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;
        $constants = json_decode($request->constants);


        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        $type = TransfusionConstant::where(["transfusion_id" => $transfusion->id])->orderBy('id', 'DESC')->first()->type;
        try {
            $transfusion->hemo_file = 1;
            $transfusion->save();
            generateHemovigilanceFile($idTransfusion);

            foreach ($constants as $key => $value) {
                TransfusionConstant::create([
                    'transfusion_id' => $transfusion->id,
                    'name' => $key,
                    'value' => $value,
                    'type' => $type,
                    'status' => 1
                ]);
            }

            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());

            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function addReaction(Request $request, $idTransfusion)
    {
        $args = array();
        $args['error'] = false;
        $name = $request->name;

        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        try {
            TransfusionReaction::create([
                'transfusion_id' => $transfusion->id,
                'name' => $name,
                'status' => 1
            ]);

            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $idTransfusion)->first());

            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
    /**
     * @group  Api Hospital Transfusion
     *
     */
    public function deleteReaction(Request $request, $idReaction)
    {
        $args = array();
        $args['error'] = false;


        $transfusionReaction = TransfusionReaction::where('id', '=', $idReaction)->first();
        try {
            $transfusionReaction->delete();

            $transfusion = new TransfusionResource(Transfusion::where('id', '=', $transfusionReaction->transfusion_id)->first());

            $args['transfusion'] = $transfusion;
            $args['message'] = "Transfusion mise à jour avec succès!";
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
}
