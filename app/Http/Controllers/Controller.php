<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *     @OA\Info(
 *         version="1.0",
 *         title="Safeblood Swagger API",
 *         description="Safeblood Swagger API Documentation",
 *     )
 *     @OA\SecurityScheme(
 *         type="http",
 *         description="Use a JWT token",
 *         name="Authorization",
 *         in="header",
 *         scheme="bearer",
 *         bearerFormat="JWT",
 *         securityScheme="bearerAuth",
 *     )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
