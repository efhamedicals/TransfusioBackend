<?php

namespace App\Http\Controllers\Api\BloodAssurance;

use App\Http\Controllers\Controller;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Récupérer les données générales du tableau de bord
     *
     * @OA\Get(
     *     path="/api/blood-assurance/dashboard/{idAssurance}/home",
     *     tags={"Api BloodAssurance Dashboard"},
     *     summary="Récupérer les données générales du tableau de bord",
     *     operationId="homeDatas",
     *     @OA\Parameter(
     *         name="idAssurance",
     *         in="path",
     *         required=true,
     *         description="ID de l'assurance",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réponse avec ou sans erreur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Succès ou message d'erreur")
     *         )
     *     )
     * )
     */
    public function homeDatas(Request $request, $idAssurance)
    {
        $args = array();
        $args['error'] = false;
        try {
            // Logique à ajouter ici
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }

        return response()->json($args);
    }

    /**
     * Liste des prescriptions associées à une assurance
     *
     * @OA\Get(
     *     path="/api/blood-assurance/dashboard/{idAssurance}/prescriptions",
     *     tags={"Api BloodAssurance Dashboard"},
     *     summary="Récupère les prescriptions liées à une assurance (hospital_id)",
     *     operationId="prescriptionsList",
     *     @OA\Parameter(
     *         name="idAssurance",
     *         in="path",
     *         required=true,
     *         description="ID de l'assurance (hospital_id)",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des prescriptions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="reference", type="string", example="RX12345"),
     *                     @OA\Property(property="rai", type="string", example="RAI123"),
     *                     @OA\Property(property="patient", type="string", example="John Doe"),
     *                     @OA\Property(property="user", type="string", example="user@example.com"),
     *                     @OA\Property(property="hospital", type="string", example="Hospital XYZ"),
     *                     @OA\Property(
     *                         property="products",
     *                         type="array",
     *                         @OA\Items(type="object", properties={
     *                             @OA\Property(property="product_id", type="integer", example=10),
     *                             @OA\Property(property="product_name", type="string", example="Blood Test Kit")
     *                         })
     *                     ),
     *                     @OA\Property(property="status", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T18:58:05.375Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-10T18:58:05.375Z"),
     *                     @OA\Property(property="bloodBag", type="string", example="Bag12345", nullable=true)
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function prescriptions(Request $request, $idAssurance)
    {
        return response()->json(
            PrescriptionResource::collection(
                Prescription::where(['hospital_id' => $idAssurance])
                    ->where('status', '!=', 1)
                    ->orderBy('created_at', 'DESC')
                    ->get()
            )
        );
    }
}
