<?php

namespace App\Http\Controllers\Api\BloodAssurance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user
     *
     * @OA\Post(
     *     path="/api/blood-assurance/login",
     *     tags={"Api Authentification BloodAssurance"},
     *     summary="Connexion d'un utilisateur via email et mot de passe",
     *     operationId="loginUser",
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
