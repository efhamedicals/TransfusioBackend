<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeProductBlood;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Models\Prescription;
use App\Models\TypeBlood;
use App\Models\BloodBag;
use App\Models\BloodBank;

class ApiController extends Controller
{
  /**
   * @OA\Get(
   *     path="/api/type-products",
   *     tags={"Api"},
   *     summary="Récupérer les produits sanguins disponibles",
   *     operationId="getTypeProducts",
   *     @OA\Response(
   *         response=200,
   *         description="Liste des produits sanguins avec statut actif",
   *         @OA\JsonContent(
   *             type="array",
   *             @OA\Items(
   *                 type="object",
   *                 properties={
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="name", type="string", example="Plasma"),
   *                     @OA\Property(property="status", type="integer", example=1)
   *                 }
   *             )
   *         )
   *     )
   * )
   */
  public function typeProducts(Request $request)
  {
    return response()->json(
      TypeProductBlood::where(['status' => 1])->get()
    );
  }

  /**
   * @OA\Get(
   *     path="/api/prescriptions",
   *     tags={"Api"},
   *     summary="Récupérer les prescriptions disponibles",
   *     operationId="getPrescriptions",
   *     @OA\Response(
   *         response=200,
   *         description="Liste des prescriptions avec statut 3 ou 4",
   *         @OA\JsonContent(
   *             type="array",
   *             @OA\Items(
   *                 type="object",
   *                 properties={
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="status", type="integer", example=3),
   *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-04-14T12:00:00Z")
   *                 }
   *             )
   *         )
   *     )
   * )
   */
  public function prescriptions(Request $request)
  {
    return response()->json(
      PrescriptionResource::collection(Prescription::where('status', '=', 3)->orWhere('status', '=', 4)->orderBy('created_at', 'DESC')->get())
    );
  }

  /**
   * @OA\Post(
   *     path="/api/confirm-request/{idPrescription}",
   *     tags={"Api BloodBank Dashboard"},
   *     summary="Confirmer la disponibilité d'une prescription",
   *     operationId="confirmPrescriptionRequest",
   *     @OA\Parameter(
   *         name="idPrescription",
   *         in="path",
   *         required=true,
   *         description="ID de la prescription",
   *         @OA\Schema(type="integer", example=1)
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"blood_center_id"},
   *             @OA\Property(property="blood_center_id", type="integer", example=2)
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Prescription confirmée avec succès",
   *         @OA\JsonContent(
   *             @OA\Property(property="error", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Disponibilité de la prescription confirmée avec succès!")
   *         )
   *     ),
   *     @OA\Response(
   *         response=400,
   *         description="Erreur lors de la confirmation de la prescription",
   *         @OA\JsonContent(
   *             @OA\Property(property="error", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Une erreur s'est produite")
   *         )
   *     )
   * )
   */
  public function confirmRequest(Request $request, $idPrescription)
  {
    $args = array();
    $args['error'] = false;
    $bloodCenterID = $request->get('blood_center_id');
    try {
      $prescription = Prescription::where(['id' => $idPrescription])->first();
      if (BloodBag::where([
        'type_product_blood_id' => $prescription->products[0]->type_product_blood_id,
        'format' => $prescription->products[0]->format,
        'type_blood_id' => TypeBlood::where(['name' => $prescription->patient->blood_type . $prescription->patient->rhesus])->first()->id,
      ])->first()) {
        $bloodBag = BloodBag::where([
          'type_product_blood_id' => $prescription->products[0]->type_product_blood_id,
          'format' => $prescription->products[0]->format,
          'type_blood_id' => TypeBlood::where(['name' => $prescription->patient->blood_type . $prescription->patient->rhesus])->first()->id,
        ])->first();
        $bloodBag->update([
          'blood_bank_id' => BloodBank::where(['id' => $prescription->hospital_id])->first()->id,
        ]);

        Prescription::where(['id' => $idPrescription])->update([
          'status' => 6,
          'blood_bag_id' => $bloodBag->id,
        ]);

        $args['message'] = "Disponibilité de la prescription confirmée avec succès!";
      } else {
        $args['error'] = true;
      }
    } catch (\Exception $e) {
      $args['error'] = true;
      $args['message'] = $e->getMessage();
    }
    return response()->json($args);
  }
}
