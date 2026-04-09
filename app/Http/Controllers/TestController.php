<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use App\Models\User;
use App\Models\UserPackageSubscription;

use App\Models\PrizeDraw;
use DrewM\MailChimp\MailChimp;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Illuminate\Support\Facades\DB;





class TestController extends Controller
{
    /**
     * Send an OTP via SMS with Twilio (supports image + link).
     */


    public function getDataPathStats(Request $request)
{
    $days = $request->get('days', 300);
    
    $dataPath = UserPackageSubscription::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays($days))
        ->where('type', 'package')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    return response()->json($dataPath);
}



    public function testcheck(Request $request)
    {

        $packageTiers = [
            8 => 'gold',
            20 => 'silver',
            21 => 'bronze',
        ];

        // Get total packages purchased (all three tiers)
        $total = UserPackageSubscription::whereIn('plan_id', [8, 20, 21])->count();

    // Get count by each tier
        $byTier = [
            'bronze' => UserPackageSubscription::where('plan_id', 8)->count(),
            'silver' => UserPackageSubscription::where('plan_id', 20)->count(),
            'gold' => UserPackageSubscription::where('plan_id', 21)->count(),
        ];

        $packageStats= [
            'total' => $total,
            'by_tier' => $byTier,
        ];

        // echo "<pre>";
        // print_r($packageStats);


        return view('consumer.chart', compact('packageStats'));

phpinfo();

$start = Carbon::now()->addHours(24)->subMinutes(5);
        $end = Carbon::now()->addHours(24)->addMinutes(10);
$subscriptions = UserPackageSubscription::query()
    ->where('type', 'package')
    ->where('status', 'active')
    ->where('package_expiry_reminder', 'no')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('user_package_subscriptions as ups2')
            ->whereColumn('ups2.user_id', 'user_package_subscriptions.user_id')
            ->whereColumn('ups2.id', '!=', 'user_package_subscriptions.id')
            ->where('ups2.status', 'active');
    })
    ->with('user')
    ->distinct()
    ->pluck('user_id');



    echo "<pre>";
    print_r($subscriptions);





        // $subscription = UserPackageSubscription::with('user')->findOrFail('f3172e79-3457-4264-b6f1-46232c35d251');
        // $subscription->update(['package_expiry_reminder' => 'no']);


        // echo "i am heree ";


        exit();
        
        $allowedUserIds =is_allowed_user();
        // $start = Carbon::now()->addDay(7)->addHours(4)->subMinutes(94);
        // $end = Carbon::now()->addDay(7)->addHours(3)->addMinutes(5);

        $start = Carbon::now()->addHours(24)->subMinutes(5);
        $end = Carbon::now()->addHours(24)->addMinutes(5);

        $start = Carbon::now()->addHours(24)->subMinutes(10);
        $end = Carbon::now()->addHours(24)->addMinutes(10);

        echo 'Packages found nearing expiry. (Checking between ' . $start . ' and ' . $end  . ')'; echo "<br>";
        echo 'Packages found nearing expiry. (Checked at: ' . Carbon::now()->toDateTimeString() . ')';





        $draws = PrizeDraw::where('winner_user_finalised', 'no')
        ->where('retarget_past_customers', 'no') 
        ->whereBetween('draw_date', [$start, $end])
        ->get();


        // Get all user IDs with any past or inactive subscriptions
        $pastUserIds = UserPackageSubscription::whereIn('status', ['canceled', 'expired'])
        ->pluck('user_id')
        ->unique();

    // Get all user IDs with active subscriptions
        $activeUserIds = UserPackageSubscription::where('status', 'active')
        ->pluck('user_id')
        ->unique();

    // Find the users who are in the 'past' list but not the 'active' list
        $lapsedUserIds = $pastUserIds->diff($activeUserIds);

        echo "<pre>";
        print_r($draws);

        exit();

        //  echo "<pre>";
        // print_r($lapsedUserIds);

        $lapsedUserIds=array('e88822d3-cbd5-4126-9f68-d8b55894bded','c2a46676-12fb-4541-a5a8-7c352a3d3694','7eb971a0-2ae9-445a-953c-42bab2b9a269');
        $lapsedUsers = User::whereIn('id', $lapsedUserIds)->get();
        // echo "<pre>";
        // print_r($lapsedUsers);

         foreach ($draws as $draw) {


            PrizeDraw::where('id', $draw->id)->update([
                'retarget_past_customers' => 'yes',
                'draw_date' => $draw->draw_date, 
                'draw_datetime' => $draw->draw_datetime, 
            ]);


            echo 'Processing draw: ' . $draw->title; echo "<br>";
        // Loop through each lapsed user and send them a notification for this specific draw
            foreach ($lapsedUsers as $user) {

            //    if (!in_array($user->id, is_allowed_user())) {
            //     continue; 
            // }



                
            echo 'Sent retargeting email to user: ' . $user->email;echo "<br>";

                   try {
                    $rawPhone = $user->mobile; 
                    $defaultRegion = 'IN'; 
                   // $defaultRegion = env('DEFAULT_REGION'); 
                    $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);

                    if (!$phoneInfo['valid']) {
                        echo "❌ Invalid phone number for user {$user->email}: {$rawPhone}"; echo "<br>";
                        continue;
                    }

                    $phoneNumber = $phoneInfo['formatted']; 
                    $country = $phoneInfo['country'];
                    $otp = mt_rand(100000, 999999);
                    $link = env('APP_URL');

                    $prizeAmount = '';
                    if ($draw->prize_type === 'cash') {
                        $prizeAmount = '$' . number_format($draw->cash_amount, 2);
                    } elseif ($draw->prize_type === 'non_cash') {
                        if (!empty($draw->prize_value_amount)) {
                            $prizeAmount .= '$' . number_format($draw->prize_value_amount, 2);
                        }
                    }

                    $message = "🔥 Don’t sit this one out, {$user->name}! \n\n"
                    . "Tomorrow’s *{$draw->name}* draw is for 💰 {$prizeAmount}! \n\n"
                    . "Reactivate your package or subscription today to get back in the game 👉 {$link}";

                    $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

                    if ($response['status'] === 'success') {
                        echo "✅ SMS sent to {$phoneNumber} ({$country})"; echo "<br>";
                    } else {
                        echo "⚠️ SMS failed for {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'); echo "<br>";
                    }
                } catch (Exception $e) {
                   echo"💥 Exception for {$user->email}: " . $e->getMessage(); echo "<br>";
                }

               // $user->notify(new RetargetPastCustomerNotification($draw));
                echo 'Sent retargeting email to user: ' . $user->email . ' for draw: ' . $draw->name; echo "<br>";
           



        }
       // $this->info('Retargeting emails sent successfully.'.$draw);   
    }

    }



public function popupcheck(Request $request)
{
    return view('consumer.popuop');
}

public function sendOtp(Request $request)
{


Stripe::setApiKey(env('STRIPE_SECRET'));

// $stripeSubscription = StripeSubscription::retrieve('sub_1ScKdH00C7GZTyzs73DH4IYR');

// echo "<pre>";
// print_r($stripeSubscription);


//    \Stripe\Subscription::update(
//         $stripeSubscription->id,
//         [
//             'items' => [
//                 [
//                     'id' => $stripeSubscription->items->data[0]->id,
//                     'price' => 'price_1SGLlp00C7GZTyzsWby4an3t',
//                 ],
//             ],
//             'proration_behavior' => 'none', 
//         ]
//     );
//    exit();

$oldPriceId = 'price_1QMSOW00C7GZTyzsJlWS0x8P';  // the Stripe price ID for $6.60  

$newPriceId = 'price_1SN5T000C7GZTyzsxrAD6vnZ';    // the Stripe price ID for $5  

// $users = \App\Models\User::whereHas('subscriptions', function($q) {
//     $q->where('stripe_status', 'active');
// })->get();


// $users = \App\Models\User::whereHas('subscriptions', function ($q) {
//     $q->where('stripe_status', 'active')
//       ->where('stripe_price', 'price_1QMSOW00C7GZTyzsJlWS0x8P');
// })->get();


$userIds = [
    '0348fc14-f180-4247-8fff-bde394e76dd7',
];
$users = User::whereIn('id', $userIds)->get();




// echo "<pre>";
// print_r($users);
// exit();
$stripeSubscriptionarray = array();
$stripeSubscriptionemailarray = array();
foreach ($users as $user) {



    $stripe_id_check=$user->subscription()->stripe_id;
    array_push($stripeSubscriptionarray, $user->subscription()->stripe_id);
    array_push($stripeSubscriptionemailarray, $user->email);

    $stripeSubscription = StripeSubscription::retrieve($user->subscription('default')->stripe_id);

    // echo "<pre>";
    // print_r($stripeSubscription);
    // exit();

    \Stripe\Subscription::update(
        $stripeSubscription->id,
        [
            'items' => [
                [
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $newPriceId,
                ],
            ],
            'proration_behavior' => 'none', 
        ]
    );





}


echo "<pre>";
print_r($stripeSubscriptionarray);

echo "<pre>";
print_r($stripeSubscriptionemailarray);






exit();


    $mailchimp = new MailChimp(env('MAILCHIMP_APIKEY'));
        $list_id = env('MAILCHIMP_LIST_ID');
        $email = strtolower('test@gmail.com');
        $subscriberHash = md5($email);
        $result = $mailchimp->put("lists/$list_id/members/$subscriberHash", [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
        ]);

        echo "<pre>";
        print_r($result);
        

        exit();


$users = User::whereIn('id', function($query) {
    $query->select('user_id')
          ->from('user_package_subscriptions')
          ->groupBy('user_id')
          ->havingRaw("SUM(CASE WHEN type = 'subscription' THEN 1 ELSE 0 END) = 0")
          ->havingRaw("SUM(CASE WHEN type = 'package' THEN 1 ELSE 0 END) > 0");
})->pluck('id')->toArray();

echo "<pre>";
print_r($users);


$users = User::whereIn('id', function($query) {
    $query->select('user_id')
          ->from('user_package_subscriptions')
          ->groupBy('user_id')
          ->havingRaw("SUM(CASE WHEN type = 'subscription' THEN 1 ELSE 0 END) = 0")   // no subscription
          ->havingRaw("SUM(CASE WHEN type = 'package' THEN 1 ELSE 0 END) > 0")        // has packages
          ->havingRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) = 0");      // no active package
})->pluck('id')->toArray();


echo "<pre>";
print_r($users);

exit();




    try {
        $rawPhone = $request->input('phone', '610401200701'); // example input
        $defaultRegion = 'AU'; // change to 'AU' when testing Australia

        // Detect and format phone
        $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);

        if (!$phoneInfo['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid phone number',
                'details' => $phoneInfo,
            ], 400);
        }

        $phoneNumber = $phoneInfo['formatted']; // e.g. +916280793457
        $country     = strtoupper($phoneInfo['country']); // e.g. IN or AU
        $otp         = mt_rand(100000, 999999);

        $link = 'https://phplaravel-1476201-5589967.cloudwaysapps.com/';
        $message = "🎉 Hello!\n\n"
            . "Your OTP is: {$otp}\n"
            . "Please visit our site: 🌐 {$link}\n\n"
            . "Thank you for staying with us! 💛";

        // ✅ Custom sender logic
        $fromName = 'XHALE'; // must be 3–11 uppercase letters (no spaces)
        $fromNumber = env('TWILIO_FROM_NAME'); // your registered Twilio number

        // Supported alphanumeric countries (add more as needed)
        $supportedSenderCountries = ['AU', 'GB', 'SG', 'NZ', 'MY', 'PH']; 

        // if (in_array($country, $supportedSenderCountries)) {
        //     config(['twilio.from' => $fromName]); // Use brand name
        // } else {
        //     config(['twilio.from' => $fromNumber]); // Use Twilio number
        // }

        config(['twilio.from' => $fromName]);

        // Send SMS
        $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['status'] === 'success'
                ? "OTP sent successfully from " . (in_array($country, $supportedSenderCountries) ? $fromName : $fromNumber)
                : $response['error'],
            'country_detected' => $country,
            'formatted_number' => $phoneNumber,
            'sender_used' => in_array($country, $supportedSenderCountries) ? $fromName : $fromNumber,
            'response' => $response,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exception: ' . $e->getMessage()
        ], 500);
    }
}




public function reprocessOfferImages()
{
    $sourceDir = 'offers/image_files';
    $targetDir = 'offers/reprocessed_images';
    $disk = Storage::disk('public');

    $files = $disk->files($sourceDir);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

    echo "🔄 Starting image reprocessing...\n";

    foreach ($files as $filePath) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            echo "⏩ Skipped non-image file: {$filePath}\n";
            continue;
        }

        try {
            reprocessStoredImage(basename($filePath), $sourceDir, $targetDir);
        } catch (\Exception $e) {
            echo "❌ Failed: {$filePath} - " . $e->getMessage() . "\n";
        }
    }

    echo "\n✅ All images reprocessed and saved in {$targetDir}\n";
}





    // public function sendOtp(Request $request)
    // {
    //     try {
    //         // Generate a 6-digit OTP
    //         $otp = mt_rand(100000, 999999);

    //         // Normally you'd get this from the request
    //         $phoneNumber = '+916280793457';

    //         // Create message
    //         $link = 'https://yourdomain.com/update-profile?user_id=123'; // dynamic link
    //         $message = "Your OTP is: {$otp}\n\nHi! Please update your account details here: {$link}";

    //         // Optional image URL (must be HTTPS and publicly accessible)
    //         $imageUrl = "https://phplaravel-1476201-5589967.cloudwaysapps.com/storage/offers/image_files/offer_097ca907-14cb-4a72-8470-4ac5ce8be6cd_0a854045-0cb3-46fc-b6a7-7417e0f41421.jpg";

    //         // Send the SMS using SmsService
    //         $response = SmsService::sendSms($phoneNumber, $message, $imageUrl);

    //         if ($response['status'] === 'failed') {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'SMS failed: ' . ($response['error'] ?? 'Unknown error')
    //             ], 500);
    //         }

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'OTP sent successfully.',
    //             'twilio_status' => $response['twilio_status'] ?? null,
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Exception: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
