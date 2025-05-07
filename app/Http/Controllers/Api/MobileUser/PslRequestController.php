<?php

namespace App\Http\Controllers\Api\MobileUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\PslRequest as ResourcesPslRequest;
use App\Models\Payment;
use App\Models\PslRequest;
use App\Models\PslRequestProduct;
use App\Models\User;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PslRequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/transfusio/psl-requests",
     *     summary="Liste des demandes PSL",
     *     description="Cette route permet de obtenir la liste des demandes de produits sanguins.",
     *     operationId="indexPslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des demandes de produits sanguins",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Liste des demandes de produits sanguins"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Non autorisé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function index(Request $request)
    {
        $pslRequests = PslRequest::where('user_id', Auth::guard('api')->user()->id)->where('status', '!=', 'processing')->get();


        return response()->json([
            'status' => true,
            'message' => 'Liste des demandes de produits sanguins',
            'data' => ResourcesPslRequest::collection($pslRequests)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/transfusio/stats",
     *     summary="Statistiques des demandes PSL",
     *     description="Cette route permet d'obtenir les statistiques des demandes de produits sanguins.",
     *     operationId="stats",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des demandes de produits sanguins",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistiques des demandes de produits sanguins"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="psl_requests_count", type="integer", example=10),
     *                 @OA\Property(property="payments_count", type="integer", example=5),
     *                 @OA\Property(property="payments_amount", type="integer", example=5000),
     *                 @OA\Property(property="average_hours", type="integer", example=2)
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Non autorisé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function stats(Request $request)
    {
        $pslRequestsLimit = ResourcesPslRequest::collection(PslRequest::where('user_id', Auth::guard('api')->user()->id)
            ->where('end_verification', '!=', null)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get());

        $pslRequestsCount = PslRequest::where('user_id', Auth::guard('api')->user()->id)->count();
        $paymentsCount = Payment::where('user_id', Auth::guard('api')->user()->id)->count();
        $paymentsAmount = Payment::where('user_id', Auth::guard('api')->user()->id)->sum('amount');

        $pslRequests = PslRequest::where('user_id', Auth::guard('api')->user()->id)
            ->whereNotNull('end_verification')
            ->get();

        $averageHours = 0;
        if (count($pslRequests) > 0) {
            $totalDuration = 0;
            $count = 0;

            foreach ($pslRequests as $request) {
                $createdAt = Carbon::parse($request->created_at);
                $endVerification = Carbon::parse($request->end_verification);
                $duration = $endVerification->diffInSeconds($createdAt);
                $totalDuration += $duration;
                $count++;
            }

            $averageSeconds = $totalDuration / $count;
            $averageHours = number_format($averageSeconds / 3600, 3);
        }


        return response()->json([
            'status' => true,
            'message' => 'Stats des demandes de produits sanguins',
            'psl_requests' => $pslRequestsLimit,
            'psl_requests_count' => $pslRequestsCount,
            'payments_count' => $paymentsCount,
            'payments_amount' => $paymentsAmount,
            'average_hours' => $averageHours
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/transfusio/psl-requests",
     *     summary="Créer une nouvelle demande PSL avec prescription et rapport sanguin",
     *     description="Cette route permet de soumettre une demande de produits sanguins à partir d'une prescription médicale et d'un rapport sanguin.",
     *     operationId="storePslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "hospital_name", "prescription", "blood_report"},
     *                 @OA\Property(property="first_name", type="string", example="Jean"),
     *                 @OA\Property(property="last_name", type="string", example="Dupont"),
     *                 @OA\Property(property="hospital_name", type="string", example="Hôpital Général de Lomé"),
     *                 @OA\Property(property="prescription", type="file", format="binary"),
     *                 @OA\Property(property="blood_report", type="file", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription traitée ou non reconnue",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prescription traitée avec succès"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Données non valides"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function store(Request $request)
    {
        $user = User::find(Auth::guard('api')->user()->id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'hospital_name' => 'required|string',
            'prescription' => 'required|image',
            'blood_report' => 'required|image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Données non valides',
                'errors' => $validator->errors()
            ], 422);
        }

        $prescriptionFile = uploadFile($request, $request->file('prescription'));

        $bloodReportFile = uploadFile($request, $request->file('blood_report'));




        $jsonData = getPrescriptionData(config('app.url') . $prescriptionFile);

        // Vérifier si le décodage a réussi et si les données sont valides
        if (json_last_error() === JSON_ERROR_NONE && isset($jsonData['is_valid'])) {
            // Si la prescription est valide
            if ($jsonData['is_valid']) {
                $data = $jsonData['data'];

                $pslRequest = PslRequest::create([
                    'user_id' => $user->id,
                    'reference' => "TRANS-PSL-" . getRamdomInt(5),
                    'prescription' => $prescriptionFile,
                    'blood_report' => $bloodReportFile,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'hospital_name' => $request->hospital_name
                ]);


                // Mettre à jour les informations de base
                $pslRequest->prescription_date = $data['prescription_date'];
                $pslRequest->prescription_fullname = $data['prescription_fullname'];
                $pslRequest->prescription_birth_date = $data['prescription_birth_date'];
                $pslRequest->prescription_age = $data['prescription_age'];
                $pslRequest->prescription_gender = $data['prescription_gender'];
                $pslRequest->prescription_blood_type = $data['prescription_blood_type'];
                $pslRequest->prescription_blood_rh = $data['prescription_blood_rh'];
                $pslRequest->prescription_diagnostic = $data['prescription_diagnostic'];
                $pslRequest->prescription_substitution = $data['prescription_substitution'];

                // Sauvegarder les modifications
                $pslRequest->save();

                // Mettre à jour les quantités de produits demandés
                foreach ($data['products'] as $product) {
                    PslRequestProduct::create([
                        'psl_request_id' => $pslRequest->id,
                        'name' => $product['name'],
                        'blood_type' => $product['blood_type'],
                        'blood_rh' => $product['blood_rh'],
                        'count' => $product['count']
                    ]);
                }

                $pslRequest = PslRequest::find($pslRequest->id);


                return response()->json([
                    'status' => true,
                    'message' => 'Demande traitée avec succès',
                    'psl_request' => new ResourcesPslRequest($pslRequest),
                    'verification' => null
                ]);
            } else {
                // Si la prescription n'est pas valide
                return response()->json([
                    'status' => false,
                    'message' => 'Document non valide ou non reconnu comme prescription'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erreur dans la réponse du service d\'analyse',
                'error' => json_last_error_msg()
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transfusio/psl-requests/{pslRequestId}/check",
     *     summary="Vérifer une demande de produits sanguins",
     *     description="Cette route permet de vérifier une demande de produits sanguins.",
     *     operationId="checkPslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pslRequestId",
     *         in="path",
     *         description="ID de la demande de produits sanguins",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription traitée ou non reconnue",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prescription traitée avec succès"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Données non valides"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function check(Request $request, $pslRequestId)
    {
        $pslRequest = PslRequest::find($pslRequestId);

        $pslRequest->end_verification = now();
        $pslRequest->status = 'waiting_payment';

        $checkingResponse = verifyBloodInStock($pslRequest);

        if ($checkingResponse['found']) {
            $pslRequest->status = 'found';
        } else {
            $pslRequest->status = 'not_found';
        }

        $pslRequest->save();

        $pslRequest = PslRequest::find($pslRequest->id);


        return response()->json([
            'status' => true,
            'message' => 'Demande vérifiée avec succès',
            'psl_request' => new ResourcesPslRequest($pslRequest),
            'verification' => $checkingResponse
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/transfusio/psl-requests/{pslRequestId}/re-check",
     *     summary="Re-vérifier une demande de produits sanguins",
     *     description="Cette route permet de re-vérifier une demande de produits sanguins.",
     *     operationId="reCheckPslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pslRequestId",
     *         in="path",
     *         description="ID de la demande de produits sanguins",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription traitée ou non reconnue",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Prescription traitée avec succès"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Données non valides"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function reCheck(Request $request, $pslRequestId)
    {
        $pslRequest = PslRequest::find($pslRequestId);

        $checkingResponse = verifyBloodInStock($pslRequest);

        if ($checkingResponse['found']) {
            $pslRequest->status = 'found';
        } else {
            $pslRequest->status = 'not_found';
        }

        $pslRequest->save();

        $pslRequest = PslRequest::find($pslRequest->id);


        return response()->json([
            'status' => true,
            'message' => 'Demande vérifiée avec succès',
            'psl_request' => new ResourcesPslRequest($pslRequest),
            'verification' => $checkingResponse
        ]);
    }

    function update(Request $request) {}
    /**
     * @OA\Get(
     *     path="/api/transfusio/psl-requests/{pslRequestId}",
     *     summary="Afficher une demande PSL",
     *     description="Cette route permet de obtenir une demande de produits sanguins.",
     *     operationId="showPslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pslRequestId",
     *         in="path",
     *         description="ID de la demande de produits sanguins",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande de produits sanguins obtenue avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Liste des demandes de produits sanguins"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="prescription", type="string", example="path/to/prescription.pdf"),
     *                 @OA\Property(property="blood_report", type="string", example="path/to/blood_report.pdf"),
     *                 @OA\Property(property="end_verification", type="string", example="2023-06-01 10:00:00"),
     *                 @OA\Property(property="prescription_date", type="string", example="2023-06-01"),
     *                 @OA\Property(property="prescription_fullname", type="string", example="John Doe"),
     *                 @OA\Property(property="prescription_birth_date", type="string", example="1990-01-01"),
     *                 @OA\Property(property="prescription_age", type="integer", example=30),
     *                 @OA\Property(property="prescription_gender", type="string", example="M"),
     *                 @OA\Property(property="prescription_blood_type", type="string", example="A+"),
     *                 @OA\Property(property="prescription_blood_rh", type="string", example="+"),
     *                 @OA\Property(property="prescription_diagnostic", type="string", example="Diagnostique"),
     *                 @OA\Property(property="prescription_substitution", type="string", example="Substitution"),
     *                 @OA\Property(property="created_at", type="string", example="2023-06-01 10:00:00"),
     *                 @OA\Property(property="products", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="psl_request_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", example="2023-06-01 10:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-06-01 10:00:00"),
     *                 )),
     *                 @OA\Property(property="status", type="string", example="processing"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Non autorisé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function show(Request $request, $pslRequestId)
    {
        $pslRequest = PslRequest::find($pslRequestId);

        return response()->json([
            'status' => true,
            'message' => 'Liste des demandes de produits sanguins',
            'data' => new ResourcesPslRequest($pslRequest)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/transfusio/psl-requests/{pslRequestId}/pay",
     *     summary="Payer une demande de produits sanguins",
     *     description="Cette route permet de payer une demande de produits sanguins.",
     *     operationId="payPslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pslRequestId",
     *         in="path",
     *         description="ID de la demande de produits sanguins",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone_number", "network", "amount"},
     *             @OA\Property(property="phone_number", type="string", example="+22890123456"),
     *             @OA\Property(property="network", type="string", example="TMONEY"),
     *             @OA\Property(property="amount", type="number", example=100),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande de produits sanguins payée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Demande de produits sanguins payée avec succès"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1)),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Requête invalide"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Non autorisé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    function pay(Request $request, $pslRequestId)
    {
        try {

            $pslRequest = PslRequest::find($pslRequestId);

            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string',
                'network' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Le numéro de téléphone est requis',
                    'errors' => $validator->errors()
                ]);
            }

            $pslRequest->status = 'waiting_payment';
            $pslRequest->save();

            $identifier = getRamdomText(10);

            payWithPaygate($identifier, $request->amount, $request->phone_number, $request->network);

            Payment::create([
                'psl_request_id' => $pslRequest->id,
                'user_id' => $pslRequest->user_id,
                'phone_number' => $request->phone_number,
                'network' => $request->network,
                'amount' => $request->amount,
                'identifier' => $identifier,
                'payment_code' => "TRANS-PAY-" . getRamdomText(10)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Paiement initié avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/transfusio/psl-requests/{pslRequestId}",
     *     summary="Supprimer une demande de produits sanguins",
     *     description="Cette route permet de supprimer une demande de produits sanguins.",
     *     operationId="deletePslRequest",
     *     tags={"PSL Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pslRequestId",
     *         in="path",
     *         description="ID de la demande de produits sanguins",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande de produits sanguins supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Demande de produits sanguins supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Requête invalide")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Non autorisé")
     *         )
     *     )
     * )
     */
    function delete(Request $request, $pslRequestId)
    {
        try {

            $pslRequest = PslRequest::find($pslRequestId);

            $pslRequest->delete();

            return response()->json([
                'status' => true,
                'message' => 'Demande de produits sanguins supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function paygateCallback(Request $request)
    {
        $identifier =  request('identifier');
        $txReference = request('tx_reference');

        Log::info("identifier: " . $identifier . " - txReference: " . $txReference);

        $payment = Payment::where(['identifier' => $identifier])->first();

        $statusOperation = 0;
        $url = 'https://paygateglobal.com/api/v2/status';
        $params = array(
            'auth_token' => "38710af9-f48a-460f-9cc8-17ee424b7b34",
            'identifier' => $identifier
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $result = curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode(curl_exec($ch), true);

        Log::info($result);

        if ($result['status'] == '0') {
            $statusOperation = 1;
        }

        if ($statusOperation == 1 && $payment) {
            Log::info('payment success');
            $payment->status = 'success';
            $payment->save();

            $pslRequest = PslRequest::find($payment->psl_request_id);
            $pslRequest->status = 'paid';
            $pslRequest->save();


            // Sending notification
            $firebaseService = new FirebaseService();
            $result = $firebaseService->sendToDevice(
                $pslRequest->user->device_token,
                "Paiement effectué",
                'Un code QR est généré pour vous. Veuillez le présenter à la banque de sang pour obtenir vos produits sanguins.',
                []
            );
            return $result;

            // assignBloodBag($pslRequest);
        } else {
            Log::info('payment failed');
            $payment->status = 'failed';
            $payment->save();
        }
    }
}
