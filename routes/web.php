<?php

use App\Models\PslRequest;
use App\Models\Transfusion;
use App\Services\FirebaseService;
use Codesmiths\LaravelOcrSpace\Enums\Language;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Codesmiths\LaravelOcrSpace\OcrSpaceOptions;
use Codesmiths\LaravelOcrSpace\Facades\OcrSpace;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;

Route::get('/hemovigilance', function () {
    generateHemovigilanceFile(5);
    $transfusion = Transfusion::where('id', '=', 5)->first();
    return view("templates.hemovigilance", compact("transfusion"));
});

// For others functionality
Route::get('/migrate-fresh', function () {

    Artisan::call('migrate:fresh');

    Artisan::call('db:seed');


    Artisan::call('config:cache');

    Artisan::call('config:clear');

    Artisan::call('cache:clear');

    Artisan::call('route:clear');

    Artisan::call('view:clear');

    Artisan::call('clear-compiled');

    return "OK.";
});

Route::get('/clear-cache', function () {

    Artisan::call('config:cache');

    Artisan::call('config:clear');

    Artisan::call('cache:clear');

    Artisan::call('route:clear');

    Artisan::call('view:clear');

    Artisan::call('clear-compiled');

    return "OK.";
});

Route::match(['get', 'post'], '/policy', function () {
    return view('policy');
});


Route::get('/ocr-test', function () {
    /*$filePath = public_path('prescription.pdf');

    $result = OcrSpace::parseImageFile(
        $filePath,
        OcrSpaceOptions::make()
            ->language(Language::French)
            ->isTable(true)
    );

    dd($result);*/

    // $yourApiKey = getenv('GEMINI_KEY');
    $client = Gemini::client("AIzaSyB4BVDLxjCl5THx-04_CM_00Fs1AmForZ8");

    $result = $client
        ->geminiFlash()
        ->generateContent([
            "Vérifies moi ce document si c'est une prescription de produits sanguins et s'il est valide.\n
            Si c'est une prescription, recupères moi les informations importantes.\n
            Pour le nom des produits demandés, faire le matching avec ces slugs. Ce sont les slugs je veux dans le tableau : 'adult_unit_red_blood', 'children_unit_red_blood', 'standard_platelet_concentrate', 'fresh_frozen_plasma'.\n
            Répondez uniquement en format JSON valide comme la structure ci-dessous sans texte supplémentaire, ni balises de code.\n
            Assurez-vous que toutes les valeurs soient correctement échappées pour JSON :
            {{
                'is_valid': true/false,
                'data': {{
                    'precription_date': '...',
                    'precription_fullname': '...',
                    'precription_birth_date': '...',
                    'precription_age': ...,
                    'precription_gender': '...',
                    'precription_blood_type': ...,
                    'precription_blood_rh': '...,
                    'precription_diagnostic': '...',
                    'precription_substitution': true/false,
                    'products': [
                        {{
                            'name': ...,
                            'blood_type': '...',
                            'blood_rh': '...',
                            'count': ...,
                        }}
                        ...
                    ]
                }},
            }}
            ",
            new Blob(
                mimeType: MimeType::APPLICATION_PDF,
                data: base64_encode(
                    file_get_contents('https://transfusio.migbloin.tg/prescription.pdf')
                )
            )
        ]);

    $response = $result->text();

    $cleanedResponse = preg_replace('/^```json\n*/', '', $response);
    $cleanedResponse = preg_replace('/```$/', '', $cleanedResponse);

    // Décoder le JSON nettoyé
    $jsonData = json_decode($cleanedResponse, true);

    // Vérifier si le décodage a réussi et si les données sont valides
    if (json_last_error() === JSON_ERROR_NONE && isset($jsonData['is_valid'])) {
        // Si la prescription est valide
        if ($jsonData['is_valid']) {
            $data = $jsonData['data'];

            dd($data);

            // Créer ou mettre à jour l'enregistrement PslRequest
            $pslRequest = PslRequest::findOrFail(1); // ou new PslRequest();

            // Mettre à jour les informations de base
            $pslRequest->precription_date = $data['precription_date'];
            $pslRequest->precription_fullname = $data['precription_fullname'];
            $pslRequest->precription_birth_date = $data['precription_birth_date'];
            $pslRequest->precription_age = $data['precription_age'];
            $pslRequest->precription_gender = $data['precription_gender'];
            $pslRequest->precription_blood_type = $data['precription_blood_type'];
            $pslRequest->precription_rh = $data['precription_blood_rh'];
            $pslRequest->precription_diagnostic = $data['precription_diagnostic'];
            $pslRequest->precription_substitution = $data['precription_substitution'];

            // Initialiser les quantités de produits à zéro
            $pslRequest->adult_unit_red_blood = 0;
            $pslRequest->children_unit_red_blood = 0;
            $pslRequest->standard_platelet_concentrate = 0;
            $pslRequest->fresh_frozen_plasma = 0;

            // Mettre à jour les quantités de produits demandés
            foreach ($data['products'] as $product) {
                $fieldName = $product['name'];
                if (property_exists($pslRequest, $fieldName)) {
                    $pslRequest->{$fieldName} = $product['count'];
                }
            }

            // Sauvegarder les modifications
            $pslRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Prescription traitée avec succès',
                'data' => $pslRequest
            ]);
        } else {
            // Si la prescription n'est pas valide
            return response()->json([
                'success' => false,
                'message' => 'Document non valide ou non reconnu comme prescription'
            ], 422);
        }
    } else {
        // Erreur de format JSON
        return response()->json([
            'success' => false,
            'message' => 'Erreur dans la réponse du service d\'analyse',
            'error' => json_last_error_msg()
        ], 500);
    }
});

Route::get('/check-product', function () {
    $pslRequest = PslRequest::findOrFail(6);
    return verifyBloodInStock($pslRequest);
});


Route::get('/test-notif', function () {
    $firebaseService = new FirebaseService();
    $result = $firebaseService->sendToDevice(
        "eHTDo_UBSwug4sW486BgHy:APA91bFzXLSkxui_jFRWzQAgTjWVHFz_oz1solM8gv_P4M4vV9qMM2PWKVd4UrJvgg6LPFK95pVQ9ZbSNjG_jaVfu-0unSkd-JeaxDDx5avhZkbocvfAj9g",
        "Paiement effectué",
        "Un code QR est généré pour vous. Veuillez le présenter à la banque de sang pour obtenir vos produits sanguins.",
        [
            'type' => 'promo'
        ]
    );
    return $result;
});
