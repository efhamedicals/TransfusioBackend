<?php

use App\Http\Controllers\Api\MobileUser\AuthController;
use App\Http\Controllers\Api\MobileUser\PslRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("paygate-callback", [PslRequestController::class, 'paygateCallback']);

Route::get("type-products", "App\Http\Controllers\Api\ApiController@typeProducts");
Route::get("sollicitations-prescriptions", "App\Http\Controllers\Api\ApiController@prescriptions");
Route::get("sollicitations-prescriptions/{id}/confirm", "App\Http\Controllers\Api\ApiController@confirmRequest");
Route::get("sollicitations-prescriptions/{id}/cancel", "App\Http\Controllers\Api\ApiController@cancelRequest");

Route::prefix('bloodcenter')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post("login", "App\Http\Controllers\Api\BloodCenter\AuthController@login");
    });
    Route::prefix('{id}')->group(function () {
        Route::get("home-datas", "App\Http\Controllers\Api\BloodCenter\DashboardController@homeDatas");
        Route::get("bloodbanks", "App\Http\Controllers\Api\BloodCenter\DashboardController@bloodbanks");
        Route::post("bloodbanks/add", "App\Http\Controllers\Api\BloodCenter\DashboardController@addBloodBank");
        Route::get("stocks", "App\Http\Controllers\Api\BloodCenter\DashboardController@stocks");
        Route::post("stocks/add", "App\Http\Controllers\Api\BloodCenter\DashboardController@addStock");
        Route::post("stocks/assign-blood", "App\Http\Controllers\Api\BloodCenter\DashboardController@assignBlood");
        Route::get("stocks/{id2}", "App\Http\Controllers\Api\BloodCenter\DashboardController@stocksProducts");
        Route::get("hemovigilances", "App\Http\Controllers\Api\BloodCenter\DashboardController@hemovigilances");
        Route::get("prescriptions-wrong", "App\Http\Controllers\Api\BloodCenter\DashboardController@prescriptionsWrong");
        //Route::get("cashbox", "App\Http\Controllers\Api\BloodCenter\DashboardController@cashbox");
        //Route::get("prescriptions", "App\Http\Controllers\Api\BloodCenter\DashboardController@prescriptions");
    });
    Route::prefix('prescriptions')->group(function () {

        Route::get("{id}/details", "App\Http\Controllers\Api\BloodCenter\DashboardController@detailsPrescription");
    });
    // Route::prefix('cashbox')->group(function () {
    //     Route::get("{id}/confirm", "App\Http\Controllers\Api\BloodCenter\DashboardController@confirmPayment");
    // });
    // Route::prefix('prescriptions')->group(function () {
    //     Route::get("{id}/confirm", "App\Http\Controllers\Api\BloodCenter\DashboardController@confirmAvailability");
    //     Route::get("{id}/cancel", "App\Http\Controllers\Api\BloodCenter\DashboardController@cancelAvailability");
    // });
});

Route::prefix('bloodbank')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post("login", "App\Http\Controllers\Api\BloodBank\AuthController@login");
    });
    Route::prefix('{id}')->group(function () {
        Route::get("details", "App\Http\Controllers\Api\BloodBank\DashboardController@details");
        Route::get("home-datas", "App\Http\Controllers\Api\BloodBank\DashboardController@homeDatas");
        Route::get("stocks", "App\Http\Controllers\Api\BloodBank\DashboardController@stocks");
        Route::get("stocks/{id2}", "App\Http\Controllers\Api\BloodBank\DashboardController@stocksProducts");
        Route::get("cashbox", "App\Http\Controllers\Api\BloodBank\DashboardController@cashbox");
        Route::get("prescriptions", "App\Http\Controllers\Api\BloodBank\DashboardController@prescriptions");
        Route::get("hemovigilances", "App\Http\Controllers\Api\BloodBank\DashboardController@hemovigilances");
        Route::get("prescriptions-wrong", "App\Http\Controllers\Api\BloodBank\DashboardController@prescriptionsWrong");
    });
    Route::prefix('cashbox')->group(function () {
        Route::get("{id}/confirm", "App\Http\Controllers\Api\BloodBank\DashboardController@confirmPayment");
    });
    Route::prefix('prescriptions')->group(function () {
        Route::get("{id}/confirm", "App\Http\Controllers\Api\BloodBank\DashboardController@confirmAvailability");
        Route::get("{id}/details", "App\Http\Controllers\Api\BloodBank\DashboardController@detailsPrescription");
        Route::get("{id}/cancel-now", "App\Http\Controllers\Api\BloodBank\DashboardController@cancelAvailabilityNow");
        Route::get("{id}/confirm-partial", "App\Http\Controllers\Api\BloodBank\DashboardController@confirmAvailabilityPartial");
        Route::get("{id}/cancel", "App\Http\Controllers\Api\BloodBank\DashboardController@cancelAvailability");
    });
});

Route::prefix('bloodassurance')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post("login", "App\Http\Controllers\Api\BloodAssurance\AuthController@login");
    });
    Route::prefix('{id}')->group(function () {
        Route::get("home-datas", "App\Http\Controllers\Api\BloodAssurance\DashboardController@homeDatas");
        Route::get("prescriptions", "App\Http\Controllers\Api\BloodAssurance\DashboardController@prescriptions");
    });
});


Route::prefix('hospital')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post("login", "App\Http\Controllers\Api\Hospital\AuthController@login");
    });
    Route::prefix('{id}/patients')->group(function () {
        Route::get("all", "App\Http\Controllers\Api\Hospital\PatientController@all");
        Route::post("add", "App\Http\Controllers\Api\Hospital\PatientController@add");
    });
    Route::prefix('{id}/prescriptions')->group(function () {
        Route::get("all", "App\Http\Controllers\Api\Hospital\PrescriptionController@all");
        Route::post("add", "App\Http\Controllers\Api\Hospital\PrescriptionController@add");
    });
    Route::prefix('{id}/transfusions')->group(function () {
        Route::get("all", "App\Http\Controllers\Api\Hospital\TransfusionController@all");
        Route::post("add", "App\Http\Controllers\Api\Hospital\TransfusionController@add");
    });
    Route::prefix('patients')->group(function () {
        Route::get("{id}/details", "App\Http\Controllers\Api\Hospital\PatientController@details");
        Route::post("{id}/add-antecedent", "App\Http\Controllers\Api\Hospital\PatientController@addAntecedent");
        Route::post("{id}/add-constant", "App\Http\Controllers\Api\Hospital\PatientController@addConstant");
        Route::get("{id}/prescriptions", "App\Http\Controllers\Api\Hospital\PatientController@currentPrescriptions");
    });
    Route::prefix('prescriptions')->group(function () {
        Route::get("{id}/check-availability", "App\Http\Controllers\Api\Hospital\PrescriptionController@checkAvailability");
        Route::get("{id}/confirm-reception", "App\Http\Controllers\Api\Hospital\PrescriptionController@confirmReception");
    });
    Route::prefix('transfusions')->group(function () {
        Route::get("{id}/details", "App\Http\Controllers\Api\Hospital\TransfusionController@details");
        Route::get("{id}/start", "App\Http\Controllers\Api\Hospital\TransfusionController@start");
        Route::get("{id}/end", "App\Http\Controllers\Api\Hospital\TransfusionController@end");
        Route::post("{id}/update", "App\Http\Controllers\Api\Hospital\TransfusionController@update");
        Route::post("{id}/add-constants", "App\Http\Controllers\Api\Hospital\TransfusionController@addConstants");
        Route::post("{id}/generate-hemo", "App\Http\Controllers\Api\Hospital\TransfusionController@generateHemo");
        Route::post("{id}/add-reaction", "App\Http\Controllers\Api\Hospital\TransfusionController@addReaction");
        Route::post("delete-reaction/{id}", "App\Http\Controllers\Api\Hospital\TransfusionController@deleteReaction");
    });
});


// User Mobile App
Route::prefix('transfusio')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/check-password', [AuthController::class, 'checkPassword']);
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/set-password', [AuthController::class, 'setPassword']);

        Route::group(['middleware' => ['assign.guard:api', 'jwt.auth']], function () {
            Route::get('/test', function () {
                return response()->json(['message' => 'Authentification réussie']);
            });
            Route::put('/set-fcm-token', [AuthController::class, 'setFcmToken']);
        });
    });
    Route::group(['middleware' => ['assign.guard:api', 'jwt.auth']], function () {
        Route::prefix('user')->group(function () {});

        Route::get('stats', [PslRequestController::class, 'stats']);

        Route::prefix('psl-requests')->group(function () {
            Route::get('/', [PslRequestController::class, 'index']);
            Route::get('/{id}', [PslRequestController::class, 'show']);
            Route::post('/', [PslRequestController::class, 'store']);
            Route::post('/{id}/check', [PslRequestController::class, 'check']);
            Route::post('/{id}/re-check', [PslRequestController::class, 'reCheck']);
            Route::post('/{id}/pay', [PslRequestController::class, 'pay']);
            Route::delete('/{id}', [PslRequestController::class, 'delete']);
        });
    });
});
