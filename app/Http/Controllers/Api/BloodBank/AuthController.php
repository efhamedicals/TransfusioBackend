<?php

namespace App\Http\Controllers\Api\BloodBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/blood-bank/login",
     *     tags={"Api Authentification BloodBank"},
     *     summary="Connexion d'un utilisateur via email et mot de passe",
     *     operationId="bloodBankLoginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie ou échouée",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Informations récupérées avec succès!"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Données utilisateur (si la connexion réussit)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Mauvaise requête",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mot de passe erroné ou aucun compte associé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Une erreur s'est produite lors de la tentative de connexion")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $args = array();
        $args['error'] = false;
        $email = $request->email;
        $password = $request->password;
        try {
            if (User::where(['email' => $email])->first()) {
                $user = User::where(['email' => $email])->first();
                if (Hash::check($password, $user->password)) {
                    $args['user'] = new UserResource($user);
                    $args['message'] = "Informations recupérées avec succès!";
                } else {
                    $args['error'] = true;
                    $args['message'] = "Mot de passe erroné";
                }
            } else {
                $args['error'] = true;
                $args['message'] = "Aucun compte associé";
            }
        } catch (\Exception $e) {
            $args['error'] = true;
            $args['message'] = $e->getMessage();
        }
        return response()->json($args);
    }
}
