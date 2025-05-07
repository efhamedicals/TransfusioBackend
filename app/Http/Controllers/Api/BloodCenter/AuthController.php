<?php

namespace App\Http\Controllers\Api\BloodCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @group  Api Authentification BloodCenter
     *
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
