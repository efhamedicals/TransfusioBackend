<?php

use App\Http\Resources\BloodBank as ResourcesBloodBank;
use App\Models\BloodBag;
use App\Models\BloodBank;
use App\Models\Transfusion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\PDF;
use Carbon\Carbon;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

if (!function_exists('getPriority')) {
    function getPriority($typeP)
    {
        $priority = "";
        switch ($typeP) {
            case 1:
                $priority = 'Urgence vitale immédiate';
                break;
            case 2:
                $priority = 'Urgence vitale';
                break;
            case 3:
                $priority = 'Urgence relative';
                break;
            case 4:
                $priority = 'Non urgent: Prévoyance';
                break;
        }
        return $priority;
    }
}
if (!function_exists('getFormat')) {
    function getFormat($typeFormat)
    {

        return $typeFormat == 1 ? "Adulte" : "Pédiatrique";
    }
}

// Random string
if (!function_exists('getRamdomText')) {
    function getRamdomText($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
}

// Random string capitalize
if (!function_exists('getRamdomTextCapi')) {
    function getRamdomTextCapi($n)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
}
// int string
if (!function_exists('getRamdomInt')) {
    function getRamdomInt($n)
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
}

if (!function_exists('getDate')) {
    function getDate($date)
    {
        $elements = explode(" ", $date);
        return $elements[0];
    }
}

if (!function_exists('getDateFromTimestamps')) {
    function getDateFromTimestamps($date)
    {
        $elements = explode(" ", $date);
        $elements2 = explode("-", $elements[0]);
        $date = $elements2[2] . "/" . $elements2[1] . "/" . $elements2[0];
        return $date;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        $formatDates = explode("T", $date);
        $elements = explode(" ", $formatDates[0]);
        $elements2 = explode("-", $elements[0]);
        $date = $elements2[2] . "-" . $elements2[1] . "-" . $elements2[0] . " " . $elements[1];
        return $date;
    }
}

if (!function_exists('superFormatDate')) {
    function superFormatDate($date)
    {
        $formatDates = explode("T", $date);
        $elements = explode(" ", $formatDates[0]);
        $elements2 = explode("-", $elements[0]);

        $timeFormat = explode(":", $elements[1]);
        $date = $elements2[2] . "-" . $elements2[1] . "-" . $elements2[0] . " à " . $timeFormat[0] . ":" . $timeFormat[1];
        return $date;
    }
}

if (!function_exists('formatDate2')) {
    function formatDate2($date)
    {
        $elements2 = explode("/", $date);
        $date = $elements2[2] . "/" . $elements2[1] . "/" . $elements2[0];
        return $date;
    }
}

if (!function_exists('reformatDate')) {
    function reformatDate($date)
    {
        $elements2 = explode("-", $date);
        $date = $elements2[2] . "-" . $elements2[1] . "-" . $elements2[0];
        return $date;
    }
}
if (!function_exists('formatDateSimple')) {
    function formatDateSimple($date)
    {
        $elements2 = explode("-", $date);
        $date = $elements2[2] . " " . getMonthName(intval($elements2[1])) . ", " . $elements2[0];
        return $date;
    }
}

if (!function_exists('getMonthName')) {
    function getMonthName($monthOfYear)
    {
        $arrayMonth = array(
            1 => "Janvier",
            2 => "Février",
            3 => "Mars",
            4 => "Avril",
            5 => "Mai",
            6 => "Juin",
            7 => "Juillet",
            8 => "Aôut",
            9 => "Septembre",
            10 => "Octobre",
            11 => "Novembre",
            12 => "Décembre"
        );
        $month =  $arrayMonth[$monthOfYear];
        return $month;
    }
}

if (!function_exists('generateHemovigilanceFile')) {
    function generateHemovigilanceFile($idTransfusion)
    {
        $transfusion = Transfusion::where('id', '=', $idTransfusion)->first();
        $pdf = PDF::setOptions([
            'images' => true
        ])->loadView('templates.hemovigilance', compact('transfusion'))->setPaper(array(0, 0, 600, 800), 'portrait');
        //$path = public_path();
        $path = "/home/www/safebloodapi.kofcorporation.com";
        $pdf->save($path . "/fiches/fiche-hemo-$transfusion->reference.pdf");
        return "/fiches/fiche-hemo-$transfusion->reference.pdf";
    }
}


function findByEmailOrPhone($email, $phone)
{
    return User::where(function ($q) use ($email, $phone) {
        if ($email) $q->where('email', $email);
        if ($phone) $q->orWhere('phone', $phone);
    })->where('status', 1)->first();
}

function sendOtpEmail($email, $otp)
{
    Mail::raw("Votre code OTP est : $otp", function ($message) use ($email) {
        $message->to($email)
            ->subject('Code OTP');
    });
}

function sendOtpPhone($phone, $otp)
{
    // Intégrer un service de SMS ici
}

function uploadFile($request, $file)
{
    if ($file != null) {
        $extension = $file->getClientOriginalExtension();
        $fileName = 'prescription_' . getRamdomInt(6) . '.' . $extension;


        $file->move(public_path('avatars'), $fileName);

        return 'avatars/' . $fileName;
    }
    return null;
}

function checkDuplicateEmailOrPhone($request)
{
    return User::when($request->email, function ($q) use ($request) {
        return $q->where('email', $request->email);
    })->when($request->phone, function ($q) use ($request) {
        return $q->orWhere('phone', $request->phone);
    })->where('status', 1)->first();
}

function createUserFromRequest($request, $avatarPath)
{
    $data = [
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'address' => $request->address,
        'token' => getRamdomText(20),
        'type_user' => 5,
        'avatar' => $avatarPath,
        'email_verify' => $request->email_verify,
        'phone_verify' => $request->phone_verify,
        'status' => 1,
    ];

    return User::create($data);
}

function getPrescriptionData($prescriptionFile)
{
    $client = Gemini::client(env('GOOGLE_API_KEY'));

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
                    'prescription_date': '...',
                    'prescription_fullname': '...',
                    'prescription_birth_date': '...',
                    'prescription_age': ...,
                    'prescription_gender': '...',
                    'prescription_blood_type': ...,
                    'prescription_blood_rh': '+/-',
                    'prescription_diagnostic': '...',
                    'prescription_substitution': true/false,
                    'products': [
                        {{
                            'name': ...,
                            'blood_type': '...',
                            'blood_rh': '+/-',
                            'count': ...,
                        }}
                        ...
                    ]
                }},
            }}
            ",
            new Blob(
                mimeType: MimeType::IMAGE_JPEG,
                data: base64_encode(
                    file_get_contents($prescriptionFile)
                )
            )
        ]);

    $response = $result->text();

    $cleanedResponse = preg_replace('/^```json\n*/', '', $response);
    $cleanedResponse = preg_replace('/```$/', '', $cleanedResponse);

    // Décoder le JSON nettoyé
    $jsonData = json_decode($cleanedResponse, true);

    return $jsonData;
}

function verifyBloodInStock($pslRequest)
{

    $productsFound = [];
    $isAll = true;

    foreach ($pslRequest->products as $product) {
        $checking = BloodBag::join('type_bloods', 'type_bloods.id', '=', 'blood_bags.type_blood_id')
            ->join('type_product_bloods', 'type_product_bloods.id', '=', 'blood_bags.type_product_blood_id')
            ->where('type_product_bloods.name', getNameOfProduct($product['name']))
            ->where('type_bloods.name', $product['blood_type'] . $product['blood_rh'])
            ->where('blood_bags.date_expiration', '>=', Carbon::now()->format('Y-m-d'))
            ->where('blood_bags.status', 1)
            ->where('blood_bags.format', getFormatOfProduct($product['name']))
            ->get([
                'blood_bags.*'
            ]);

        if ($checking->count() >= $product['count']) {
            $bloodBanks = [];
            $amount = 0;
            for ($i = 0; $i < $product['count']; $i++) {
                $bloodBag = $checking[$i];
                $bloodBanks[] = new ResourcesBloodBank(BloodBank::find($bloodBag->blood_bank_id));
                $amount += $bloodBag->price;
            }
            $productsFound[] = [
                'id' => $product['id'],
                'count' => $product['count'],
                'bloodBanks' => $bloodBanks,
                'amount' => $amount,
                'all' => true
            ];
        } else if ($checking->count() > 0) {
            $isAll = false;
            $bloodBanks = [];
            $amount = 0;
            foreach ($checking as $bloodBag) {
                $bloodBanks[] = new ResourcesBloodBank(BloodBank::find($bloodBag->blood_bank_id));
                $amount += $bloodBag->price;
            }
            $productsFound[] = [
                'id' => $product['id'],
                'count' => $checking->count(),
                'amount' => $amount,
                'all' => false,
                'bloodBanks' => $bloodBanks
            ];
        }
    }


    return [
        'found' => count($productsFound) === 0 ? false : true,
        'is_all' => count($productsFound) === count($pslRequest->products) && $isAll,
        'data' => $productsFound
    ];
}

function assignBloodBag($pslRequest)
{

    foreach ($pslRequest->products as $product) {
        $checking = BloodBag::join('type_bloods', 'type_bloods.id', '=', 'blood_bags.type_blood_id')
            ->join('type_product_bloods', 'type_product_bloods.id', '=', 'blood_bags.type_product_blood_id')
            ->where('type_product_bloods.name', getNameOfProduct($product['name']))
            ->where('type_bloods.name', $product['blood_type'] . $product['blood_rh'])
            ->where('blood_bags.date_expiration', '>=', Carbon::now()->format('Y-m-d'))
            ->where('blood_bags.status', 1)
            ->where('blood_bags.format', getFormatOfProduct($product['name']))
            ->get([
                'blood_bags.*'
            ]);

        foreach ($checking as $bloodBag) {
            BloodBag::where('id', $bloodBag->id)->update([
                'psl_request_id' => $pslRequest->id,
                'status' => 2
            ]);
        }
    }
}


function getNameOfProduct($name)
{
    switch ($name) {
        case 'adult_unit_red_blood':
            return 'Culot globulaire';
        case 'children_unit_red_blood':
            return 'Culot globulaire';
        case 'standard_platelet_concentrate':
            return 'Concentrés de standards de plaquettes';
        case 'fresh_frozen_plasma':
            return 'Plasma frais congelé';
    }
}

function getFormatOfProduct($name)
{
    switch ($name) {
        case 'adult_unit_red_blood':
            return 1;
        case 'children_unit_red_blood':
            return 2;
        case 'standard_platelet_concentrate':
            return 1;
        case 'fresh_frozen_plasma':
            return 2;
    }
}


if (!function_exists('payWithPaygate')) {
    function payWithPaygate($identifier, $amount, $phoneNumber, $network)
    {
        $apiToken = env('PAYGATE_API_TOKEN');
        $description = 'Paiement de produits sanguins';
        $returnURL = env('NGROK_URL');

        $url = 'https://paygateglobal.com/api/v1/pay';
        $params = array(
            'auth_token' => $apiToken,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'description' => $description,
            'identifier' => $identifier,
            'network' => $network
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

        return $result;
    }
}


if (!function_exists('sendSMS')) {
    function sendSMS($phoneNumber, $content)
    {
        $api_key = env('SMS_API_KEY');
        $api_secret = env('SMS_API_SECRET');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://extranet.nghcorp.net/api/send-sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                "from" => "MiGbloin",
                "to" => $phoneNumber,
                "text" => "$content",
                "reference" => getRamdomInt(5),
                "api_key" => $api_key,
                "api_secret" => $api_secret
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response; // JSON response
    }
}



if (!function_exists('sendFCMNotification')) {
    function sendFCMNotification($title, $content, $token, $type = "simple", $data = null)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $extraNotificationData = [
            "moredata" => 'dd',
            "title" => $title,
            "body" => $content,
            "type" =>  $type,
            "ref" => $data,
        ];

        $notification = [
            'title' => $title,
            'body' => $content,
            'sound' => true,
            'data' => $extraNotificationData
        ];

        $fcmNotification = [
            'to' => $token == "" ? '/topics/migbloin-users' : '/topics/migbloin-' . $token,
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key= ' . env('FIREBASE_SERVER_KEY'),
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
