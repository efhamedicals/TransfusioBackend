<?php

namespace App\Http\Controllers\Api\MobileUser;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\CheckPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\SetFcmTokenRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Connexion d'un utilisateur via email ou téléphone
     *
     * @OA\Post(
     *     path="/api/transfusio/auth/login",
     *     tags={"Authentification"},
     *     summary="Connexion d'un utilisateur via email ou téléphone",
     *     operationId="mobileLoginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"email"},
     *                     @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *                 ),
     *                 @OA\Schema(
     *                     required={"phone"},
     *                     @OA\Property(property="phone", type="string", example="+22890123456")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie ou OTP envoyé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="message", type="string", example="Otp envoyé avec succès!")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = findByEmailOrPhone($request->email, $request->phone);

        if (!$user) {
            $otp = "000000"; //getRamdomInt(6);

            if ($request->email) {
                sendOtpEmail($request->email, $otp);
            } else {
                sendOtpPhone($request->phone, $otp);
            }

            return response()->json([
                'status' => false,
                'otp' => $otp,
                'message' => 'Otp envoyé avec succès!'
            ]);
        }

        if ($request->email && !$user->email_verify) {
            return response()->json([
                'status' => false,
                'message' => 'Cette adresse mail est reliée à un compte mais n\'est pas vérifiée. Veuillez utiliser le numéro de téléphone.'
            ]);
        }

        if ($request->phone && !$user->phone_verify) {
            return response()->json([
                'status' => false,
                'message' => 'Ce numéro de téléphone est relié à un compte mais n\'est pas vérifié. Veuillez utiliser l\'adresse mail.'
            ]);
        }

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'User\'s infos got successfully'
        ]);
    }

    /**
     * Vérification du mot de passe de l'utilisateur
     *
     * @OA\Post(
     *     path="/api/transfusio/auth/check-password",
     *     tags={"Authentification"},
     *     summary="Vérifie le mot de passe de l'utilisateur",
     *     operationId="checkPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","password"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe vérifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="api_token", type="string", example="jwt.token.here"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", type="object", description="Données utilisateur")
     *         )
     *     )
     * )
     */

    public function checkPassword(CheckPasswordRequest $request)
    {
        $user = User::where('id', $request->id)->where('status', 1)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Mot de passe incorrect']);
        }

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'status' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    /**
     * Envoi d'un OTP à l'utilisateur
     *
     * @OA\Post(
     *     path="/api/transfusio/auth/send-otp",
     *     tags={"Authentification"},
     *     summary="Envoie un OTP à l'utilisateur via email ou téléphone",
     *     operationId="sendOtp",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","phone","is_new"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="+22890123456"),
     *             @OA\Property(property="is_new", type="boolean", example=true),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="message", type="string", example="Otp envoyé avec succès!")
     *         )
     *     )
     * )
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $otp = "000000"; //getRamdomInt(6);
        $user_check = checkDuplicateEmailOrPhone($request);

        if ($user_check) {
            return response()->json([
                'status' => false,
                'message' => $request->email ? 'Cette adresse mail est déjà utilisée!' : 'Ce numéro de téléphone est déjà utilisé!'
            ]);
        }

        if ($request->email) {
            sendOtpEmail($request->email, $otp);
        } else {
            sendOtpPhone($request->phone, $otp);
        }

        return response()->json(['status' => true, 'otp' => $otp, 'message' => 'Otp envoyé avec succès!']);
    }

    /**
     * Enregistrement d'un utilisateur
     *
     * @OA\Post(
     *     path="/api/transfusio/auth/register",
     *     tags={"Authentification"},
     *     summary="Enregistre un nouvel utilisateur",
     *     operationId="registerPersonal",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","phone","password","first_name","last_name","address","email_verify","phone_verify"},
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+22890123456"),
     *                 @OA\Property(property="password", type="string", example="password"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="address", type="string", example="123 Main Street"),
     *                 @OA\Property(property="email_verify", type="boolean", example=true),
     *                 @OA\Property(property="phone_verify", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Utilisateur enregistré avec succès!")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $avatarPath = uploadFile($request, $request->file('avatar'));

        $user = createUserFromRequest($request, $avatarPath);

        return $this->checkPassword(new CheckPasswordRequest([
            'id' => $user->id,
            'password' => $request->password
        ]));
    }


    /**
     * Définir ou réinitialiser le mot de passe d'un utilisateur
     *
     * @OA\Post(
     *     path="/api/transfusio/auth/set-password",
     *     tags={"Authentification"},
     *     summary="Définit un nouveau mot de passe pour l'utilisateur",
     *     operationId="setPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","password","password_confirmation"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="password", type="string", format="password", example="nouveaumdp123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="nouveaumdp123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe défini avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mot de passe défini avec succès"),
     *             @OA\Property(property="api_token", type="string", example="jwt.token.here"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", type="object", description="Données utilisateur")
     *         )
     *     )
     * )
     */
    public function setPassword(SetPasswordRequest $request)
    {
        $user = findByEmailOrPhone($request->email, $request->phone);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Utilisateur introuvable']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['status' => true, 'message' => 'Mot de passe réinitialisé avec succès!']);
    }


    /**
     * Enregistrer ou mettre à jour le token FCM de l'utilisateur
     *
     * @OA\Put(
     *     path="/api/transfusio/auth/set-fcm-token",
     *     tags={"Authentification"},
     *     summary="Enregistre ou met à jour le token Firebase Cloud Messaging (FCM)",
     *     operationId="setFcmToken",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_token"},
     *             @OA\Property(property="device_token", type="string", example="dskfdjfklsdjfksdf-firebase-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token FCM enregistré",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token enregistré avec succès")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}  
     * )
     */

    public function setFcmToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Données non valides',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::guard('api')->user()->id);
        $user->device_token = $request->device_token;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Token assigné avec succès!']);
    }
}
