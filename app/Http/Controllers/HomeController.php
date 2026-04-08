<?php
namespace App\Http\Controllers;
use App\Enums\OrganisationStatus; 
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Plan; 
use App\Models\Organisation;
use App\Models\Offer;
use App\Models\User;
use App\Models\Events;
use App\Models\Page;
use App\Models\PrizeDraw;
use App\Models\PageMeta;
use App\Models\UnregisteredUser;

use Carbon\Carbon; // For current date
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Don't forget to import Log
use Illuminate\Support\Facades\Config; // <-- Make sure this is imported!
use Illuminate\Support\Str; 
use App\Models\Faq;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Mail; // Crucial: Import the Mail facade
use Illuminate\Support\Facades\Storage;

use App\Models\UserPackageSubscription; // Your custom subscription table model
use App\Models\PrizeDrawEntry;




use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rules\Password; 
use App\Notifications\UserCreatedNotification; // <-- Import your new notification
use App\Notifications\PurchaseConfirmationNotification; // <-- This is the key line

use App\Notifications\NewBusinessAccountNotification;
use App\Notifications\NewOrganisationNotification; // <-- Import the new notification
use App\Notifications\NewOfferNotification;
use Illuminate\Support\Facades\Notification; // <-- Import the Notification facade


use Illuminate\Support\Facades\File;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\ImageOptimizer\OptimizerChainFactory;

use App\Services\SmsService;
use App\Helpers\PhoneHelper;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }



    public function showRegisterPage($id = null)
    {
        $package_subscription_plan = null;
        if ($id) {
            $package_subscription_plan = Plan::findOrFail($id);
        }

        return view('auth.custom-register', compact('package_subscription_plan'));
    }


    public function showPackageRegister($package)
    {
        $package_subscription_plan = Plan::where('slug', $package)->first();

         return view('auth.custom-register', compact('package_subscription_plan', 'package'));
    }




//     protected function UserRegister(Request $request)
//     {
//      // Validate the request data, excluding the password field
//         $validator = Validator::make($request->all(), [
//             'name' => ['required', 'string', 'max:255'],
//             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//             'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()->withErrors($validator)->withInput();
//         }


//          $mailchimp = new MailChimp(env('MAILCHIMP_APIKEY'));
//         $list_id = env('MAILCHIMP_LIST_ID');
//         $email = strtolower($request->input('email'));
//         $subscriberHash = md5($email);
//         $result = $mailchimp->put("lists/$list_id/members/$subscriberHash", [
//             'email_address' => $email,
//             'status_if_new' => 'subscribed',
//         ]);




//       // Create the user 
//         $tempPassword = Str::random(32);
//         $user = User::create([
//             'name' => $request->input('name'),
//             'email' => $request->input('email'),
//             'mobile' => $request->input('mobile'),
//             'terms_accepted' => (int) $request->has('terms_accepted'),
//             'password' => Hash::make($tempPassword), // 🔐 temp unusable password
//         ]);


//         try {
//             $rawPhone = $user->mobile; 
//             //$defaultRegion = 'IN'; 
//             $defaultRegion = env('DEFAULT_REGION'); 
//             $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);

//             // if (!$phoneInfo['valid']) {
//             //     echo "❌ Invalid phone number for user {$user->email}: {$rawPhone}"; 
//             //     return; 
//             // }

//             $phoneNumber = $phoneInfo['formatted']; 
//             $country = $phoneInfo['country'];
//             $link = env('APP_URL');

//             // User joined date (formatted)
//             $userJoined = $user->created_at
//             ? $user->created_at->timezone('Australia/Sydney')->format('j M Y')
//             : 'recently';

//             $message =
//             "Hi {$user->name}! 👋\n\n" .
//             "Thanks for being a valued member since {$userJoined}.\n\n" .
//             "Your account is active and you're all set to enjoy member benefits.\n\n" .
//             "Visit us anytime 👉 {$link}";

//             $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

//             if ($response['status'] === 'success') {
//                 echo "✅ SMS sent to {$phoneNumber} ({$country})"; echo "<br>";
//             } else {
//                 echo "⚠️ SMS failed for {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'); echo "<br>";
//             }
//         } catch (Exception $e) {
//          echo"💥 Exception for {$user->email}: " . $e->getMessage(); echo "<br>";
//      }


//     //    echo "<pre>";
//     // print_r($_POST);

//     // exit();

//         Auth::login($user);

//         $user->notify(new UserCreatedNotification($user,$tempPassword));

//         $plan = Plan::findOrFail($request->selected_plan_id);
//         $planType = $plan->type;
//         $paymentMethodId = $request->input('payment_method');
//         $cardHolderName = $request->input('name');
//         $cardHolderEmail = $user->email;

//         try {
//             // Create Stripe customer if they don't exist
//             if (is_null($user->stripe_id)) {
//                 $user->createAsStripeCustomer([
//                     'name' => $cardHolderName,
//                     'email' => $cardHolderEmail,
//                 ]);
//             } else {
//                 $user->updateStripeCustomer([
//                     'name' => $cardHolderName,
//                     'email' => $cardHolderEmail,
//                 ]);
//             }
//             $user->addPaymentMethod($paymentMethodId);
//             $user->updateDefaultPaymentMethod($paymentMethodId);

//          // --- Handle Package Purchase (One-time charge) ---

//             $prize_draw_entries_check=false;
//             if ($planType === 'package') {
//                 $newPackageStartsAt = Carbon::now();
//                 $newPackageStatus = 'active';
//                 $newPackageEndsAt = $newPackageStartsAt->copy()->addWeeks($plan->access_duration_weeks);
//                 $charge = $user->charge(
//                     (int)($plan->price * 100), 
//                     $paymentMethodId, 
//                     [
//                         'description' => $plan->name . ' Purchase',
//                         'return_url' => route('payment.success'), 
//                         'currency' => env('DEFAULT_CURRENCY', 'aud'),
//                     ]
//                 );

//                 $userPackageSubscription = UserPackageSubscription::create([
//                     'user_id'       => $user->id,
//                     'plan_id'       => $plan->id,
//                     'type'          => $plan->type,
//                     'purchase_date' => Carbon::now(),
//                     'starts_at'     => $newPackageStartsAt,
//                     'ends_at'       => $newPackageEndsAt,
//                     'status'        => $newPackageStatus,
//                     'stripe_id'     => $charge->id, 
//                 ]);

//                 $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
//                 award_immediate_prize_entries($user, $plan, $userPackageSubscription);
//                 $success_msg = 'Package purchased successfully!';

//             } elseif ($planType === 'subscription') {
//                 // Create the new subscription
//                 $stripeSubscription = $user->newSubscription($plan->slug, $plan->stripe_plan)->create($paymentMethodId);

//                 // Store subscription details in your local database
//                 $userPackageSubscription =UserPackageSubscription::create([
//                     'user_id'       => $user->id,
//                     'plan_id'       => $plan->id,
//                     'type'          => $plan->type,
//                     'purchase_date' => Carbon::now(),
//                     'starts_at'     => Carbon::now(),
//                     'ends_at'       => Carbon::now()->addWeeks(1),
//                     'status'        => $stripeSubscription->stripe_status, // Use Stripe's status (e.g., 'active', 'incomplete')
//                     'stripe_id'     => $stripeSubscription->stripe_id, // Store Stripe Subscription ID
//                 ]);
                
//                 $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
//                 $success_msg='Subscription created successfully!';

//             }

//         //     $numberOfFreeEntries = null;   
//         //     if ($prize_draw_entries_check) {
//         //      $userTotalPackageSubscriptions = UserPackageSubscription::where('user_id', $user->id)->count();
//         //      if ($userTotalPackageSubscriptions == 1) {
//         //         $numberOfFreeEntries = 4;
//         //         for ($i = 0; $i < $numberOfFreeEntries; $i++) {
//         //             PrizeDrawEntry::create([
//         //                 'user_id'                   => $user->id,
//         //                 'package_subscriptions_id'  => $userPackageSubscription->id,
//         //                 'entries_added'             => 1, 
//         //                 'description'               => 'Free entry for first package purchase',
//         //                 'type'             => 'free', 
//         //             ]);
//         //         }
//         //     }   
//         // }
        
//         $user->notify(new PurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));
//         //return redirect()->route('subscription.manage')->with('success', $success_msg);

//         $routes = [
//             11 => 'thankyousubscription',
//             8  => 'thankyougold',
//             20 => 'thankyousilver',
//             21 => 'thankyoubronze',
//         ];
//         $route = $routes[$plan->id] ?? 'thank_you';
//         return redirect()->route($route)->with('success', $success_msg);
        

//     } catch (\Laravel\Cashier\Exceptions\PaymentActionRequiredException $e) {
//       return back()
//       ->withErrors(['stripe_error' => 'Payment requires additional authentication. Please try again or use a different payment method.'])
//       ->withInput();
//   } catch (\Laravel\Cashier\Exceptions\PaymentFailure $e) {
//     return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
// } catch (CardException $e) {
//     return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
// } catch (\Exception $e) {
//     return back()->withErrors(['error' => 'An unexpected error occurred during purchase. Please try again.'])->withInput();
// }


// }



    protected function UserRegister(Request $request)
    {
        // -------------------------------------------------------
        // Validate
        // -------------------------------------------------------
        $validator = Validator::make($request->all(), [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // -------------------------------------------------------
        // Mailchimp
        // -------------------------------------------------------
        $mailchimp      = new MailChimp(env('MAILCHIMP_APIKEY'));
        $list_id        = env('MAILCHIMP_LIST_ID');
        $email          = strtolower($request->input('email'));
        $subscriberHash = md5($email);
        $mailchimp->put("lists/$list_id/members/$subscriberHash", [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
        ]);

        // -------------------------------------------------------
        // Create user
        // -------------------------------------------------------
        $tempPassword = Str::random(32);
        $user = User::create([
            'name'           => $request->input('name'),
            'email'          => $request->input('email'),
            'mobile'         => $request->input('mobile'),
            'terms_accepted' => (int) $request->has('terms_accepted'),
            'password'       => Hash::make($tempPassword),
        ]);

        // -------------------------------------------------------
        // SMS
        // -------------------------------------------------------
        try {
            $rawPhone      = $user->mobile;
            $defaultRegion = env('DEFAULT_REGION');
            $phoneInfo     = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);
            $phoneNumber   = $phoneInfo['formatted'];
            $country       = $phoneInfo['country'];
            $link          = env('APP_URL');
            $userJoined    = $user->created_at
                ? $user->created_at->timezone('Australia/Sydney')->format('j M Y')
                : 'recently';

            $message =
                "Hi {$user->name}! 👋\n\n" .
                "Thanks for being a valued member since {$userJoined}.\n\n" .
                "Your account is active and you're all set to enjoy member benefits.\n\n" .
                "Visit us anytime 👉 {$link}";

            $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

            if ($response['status'] === 'success') {
                Log::info("SMS sent to {$phoneNumber} ({$country})");
            } else {
                Log::warning("SMS failed for {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('SMS error: ' . $e->getMessage());
        }

        // -------------------------------------------------------
        // Login + notify
        // -------------------------------------------------------
        Auth::login($user);
        $user->notify(new UserCreatedNotification($user, $tempPassword));

        $plan            = Plan::findOrFail($request->selected_plan_id);
        $planType        = $plan->type;
        $paymentMethodId = $request->input('payment_method');

        // Thank you routes
        $thankYouRoutes = [
            11 => 'thankyousubscription',
            8  => 'thankyougold',
            20 => 'thankyousilver',
            21 => 'thankyoubronze',
        ];

        // -------------------------------------------------------
        // PAYPAL FLOW — payment_method empty hai toh PayPal se aaya
        // -------------------------------------------------------
        if (empty($paymentMethodId)) {

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $currency = strtoupper(env('DEFAULT_CURRENCY', 'AUD'));

            // Session mein plan + registration flag save karo
            session([
                'paypal_plan_id'      => $plan->id,
                'paypal_type'         => $planType,
                'paypal_is_upsell'    => false,
                'paypal_reg_user_id'  => $user->id,
            ]);

            if ($planType === 'package') {
                $order = $provider->createOrder([
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'description' => $plan->name . ' Package',
                        'amount' => [
                            'currency_code' => $currency,
                            'value'         => number_format($plan->price, 2, '.', ''),
                        ],
                    ]],
                    'application_context' => [
                        'return_url' => route('paypal.success'),
                        'cancel_url' => route('paypal.cancel'),
                    ],
                ]);

                if (isset($order['id']) && $order['status'] === 'CREATED') {
                    foreach ($order['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            session(['paypal_order_id' => $order['id']]);
                            return redirect()->away($link['href']);
                        }
                    }
                }

                Log::error('PayPal Registration Order Failed', $order);
                return redirect()->back()->with('error', 'PayPal order creation failed. Please try again.');

            } else {
                // Subscription
                if (empty($plan->paypal_plan_id)) {
                    return redirect()->back()->with('error', 'This plan does not have a PayPal subscription configured.');
                }

                $subscription = $provider->createSubscription([
                    'plan_id' => $plan->paypal_plan_id,
                    'subscriber' => [
                        'name' => [
                            'given_name' => $user->name,
                            'surname'    => '',
                        ],
                        'email_address' => $user->email,
                    ],
                    'application_context' => [
                        'brand_name'  => config('app.name'),
                        'locale'      => 'en-US',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'return_url'  => route('paypal.success'),
                        'cancel_url'  => route('paypal.cancel'),
                    ],
                ]);

                if (isset($subscription['id']) && $subscription['status'] === 'APPROVAL_PENDING') {
                    session(['paypal_subscription_id' => $subscription['id']]);
                    foreach ($subscription['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            return redirect()->away($link['href']);
                        }
                    }
                }

                Log::error('PayPal Registration Subscription Failed', $subscription);
                return redirect()->back()->with('error', 'PayPal subscription creation failed. Please try again.');
            }
        }

        // -------------------------------------------------------
        // STRIPE FLOW — original logic unchanged
        // -------------------------------------------------------
        $cardHolderName  = $request->input('name');
        $cardHolderEmail = $user->email;

        try {
            if (is_null($user->stripe_id)) {
                $user->createAsStripeCustomer([
                    'name'  => $cardHolderName,
                    'email' => $cardHolderEmail,
                ]);
            } else {
                $user->updateStripeCustomer([
                    'name'  => $cardHolderName,
                    'email' => $cardHolderEmail,
                ]);
            }

            $user->addPaymentMethod($paymentMethodId);
            $user->updateDefaultPaymentMethod($paymentMethodId);

            $userPackageSubscription = null;
            $numberOfFreeEntries     = null;

            if ($planType === 'package') {
                $newPackageStartsAt = Carbon::now();
                $newPackageStatus   = 'active';
                $newPackageEndsAt   = $newPackageStartsAt->copy()->addWeeks($plan->access_duration_weeks);

                $charge = $user->charge(
                    (int)($plan->price * 100),
                    $paymentMethodId,
                    [
                        'description' => $plan->name . ' Purchase',
                        'return_url'  => route('payment.success'),
                        'currency'    => env('DEFAULT_CURRENCY', 'aud'),
                    ]
                );

                $userPackageSubscription = UserPackageSubscription::create([
                    'user_id'       => $user->id,
                    'plan_id'       => $plan->id,
                    'type'          => $plan->type,
                    'purchase_date' => Carbon::now(),
                    'starts_at'     => $newPackageStartsAt,
                    'ends_at'       => $newPackageEndsAt,
                    'status'        => $newPackageStatus,
                    'stripe_id'     => $charge->id,
                    'paypal_id'     => null,
                ]);

                $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
                award_immediate_prize_entries($user, $plan, $userPackageSubscription);
                $success_msg = 'Package purchased successfully!';

            } elseif ($planType === 'subscription') {
                $stripeSubscription = $user->newSubscription($plan->slug, $plan->stripe_plan)->create($paymentMethodId);

                $userPackageSubscription = UserPackageSubscription::create([
                    'user_id'       => $user->id,
                    'plan_id'       => $plan->id,
                    'type'          => $plan->type,
                    'purchase_date' => Carbon::now(),
                    'starts_at'     => Carbon::now(),
                    'ends_at'       => Carbon::now()->addWeeks(1),
                    'status'        => $stripeSubscription->stripe_status,
                    'stripe_id'     => $stripeSubscription->stripe_id,
                    'paypal_id'     => null,
                ]);

                // Sirf active pe entries banao
                if ($stripeSubscription->stripe_status === 'active') {
                    $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
                }
                $success_msg = 'Subscription created successfully!';
            }

            $user->notify(new PurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));

            $route = $thankYouRoutes[$plan->id] ?? 'thank_you';
            return redirect()->route($route)->with('success', $success_msg);

        } catch (\Laravel\Cashier\Exceptions\PaymentActionRequiredException $e) {
            return back()->withErrors(['stripe_error' => 'Payment requires additional authentication. Please try again or use a different payment method.'])->withInput();
        } catch (\Laravel\Cashier\Exceptions\PaymentFailure $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
        } catch (\Stripe\Exception\CardException $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred during purchase. Please try again.'])->withInput();
        }
    }





private function getUniqueFileName($originalName, $directory)
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $uniqueFilename = $originalName;
    $counter = 0;
    while (Storage::disk('public')->exists($directory . '/' . $uniqueFilename)) {
        $counter++;
        $uniqueFilename = $filenameWithoutExt . '-' . $counter . '.' . $extension;
    }
    return $uniqueFilename;
}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {



        $countdownEndDate= null;
        
        $nowBrisbane = Carbon::now('Australia/Brisbane');
        $latestDraw = PrizeDraw::where('draw_date', '>=', $nowBrisbane)
        ->where('winner_user_finalised', 'no')
        ->where('show_on_site', true)
        ->whereNull('winner_id')
        ->orderBy('draw_date', 'asc')
        ->orderBy('created_at', 'desc')
        ->first();


        if ($latestDraw) {
            $countdownEndDate = $latestDraw->draw_date;
        }



        $page = Page::findOrFail(1);
        $metaData = $page->meta->pluck('meta_value', 'meta_key')->toArray();

        $tabs = ['work', 'rewards', 'community', 'lifestyle'];
        $tabData = [];
        foreach ($tabs as $tab) {
            $tabData[$tab] = [
                'title_line1' => $metaData["{$tab}_tab_title_line1"] ?? '',
                'title_line2' => $metaData["{$tab}_tab_title_line2"] ?? '',
                'paragraph'   => $metaData["{$tab}_tab_paragraph"] ?? '',
                'image'       => $metaData["{$tab}_tab_image"] ?? '',
            ];
        }

        $today = Carbon::today();
       

        $is_featured_offers = Offer::with(['organisation', 'categories'])
        ->where('is_featured', 'yes')
        ->where('end_date', '>=', Carbon::now()) 
        ->get();

        $plans =Plan::orderBy('position')->where('is_public', true)->get();
        $organisations = Organisation::where('processing_status', OrganisationStatus::APPROVED)->get();
        // $categories = Category::orderBy('position')->take(10)->get();


        $categories = Category::orderBy('position')
        ->with(['offers' => function($query) {
            $query->where('end_date', '>=', Carbon::now());
        }, 'offers.attachments']) 
        ->get();



        $today = Carbon::now();
        $categories = Category::with([
            'offers' => function ($query) use ($today) {
                $query->where('start_date', '<=', $today)
                ->where('processing_status', OrganisationStatus::APPROVED)
                ->where(function($query) use ($today) {
                    $query->where('end_date', '>=', $today)
                    ->orWhere('no_end_date', 1); 
                })
                ->with('organisation');
            }
        ])
        ->orderBy('name')
        ->get();

        

        // Extract unique organisations from all categories/offers
        $organisations = $categories
            ->pluck('offers')
            ->flatten()
            ->pluck('organisation')
            ->unique('id')
            ->filter(fn($org) => !empty($org->image))
            ->shuffle() // Randomize the entire list first
            ->take(24)  // Then grab the first 30 from that random order
            ->values();

        // Split into two parts
        $count = $organisations->count();
        $midpoint = ceil($count / 2);

        $organisations_part1 = $organisations->slice(0, $midpoint);
        $organisations_part2 = $organisations->slice($midpoint);





        $bodyClass = 'home-index';

        return view('consumer.home', compact('latestDraw','metaData','tabData','is_featured_offers','plans','organisations','categories','countdownEndDate','organisations_part1','organisations_part2','bodyClass'));
    }



public function tabOffers(Request $request, $categoryId)
{
    $today       = Carbon::now();
    $business_id = $request->input('busness');
    $searchTerm  = $request->input('search');
    $offerclass  = $request->input('offerclass', ''); // pass if needed

    $category = Category::with([
        'offers' => function ($query) use ($today) {
            $query->where('start_date', '<=', $today)
                ->where('processing_status', OrganisationStatus::APPROVED)
                ->where(function ($q) use ($today) {
                    $q->where('end_date', '>=', $today)
                      ->orWhere('no_end_date', 1);
                })
                ->with(['organisation', 'attachments']);
        }
    ])->findOrFail($categoryId);

    return view('consumer.blocks.category-organisations-offers-tab-partial', compact('category', 'business_id', 'searchTerm', 'offerclass'));
}





    private function buildPageData(int $pageId)
    {
        $plans = Plan::orderBy('position')
        ->where('is_public', true)
        ->get();

        $subscription = Plan::where('is_public', true)
        ->where('type', 'subscription')
        ->get();

        $package = Plan::where('is_public', true)
        ->where('type', 'package')
        ->get();

        $page = Page::findOrFail($pageId);
        $metaData = $page->meta->pluck('meta_value', 'meta_key')->toArray();

    // Tabs
        $tabs = ['work', 'rewards', 'community', 'lifestyle'];
        $tabData = [];

        foreach ($tabs as $tab) {
            $tabData[$tab] = [
                'title_line1' => $metaData["{$tab}_tab_title_line1"] ?? '',
                'title_line2' => $metaData["{$tab}_tab_title_line2"] ?? '',
                'paragraph'   => $metaData["{$tab}_tab_paragraph"] ?? '',
                'image'       => $metaData["{$tab}_tab_image"] ?? '',
            ];
        }

    // Countdown
        $countdownEndDate = null;
        $nowBrisbane = Carbon::now('Australia/Brisbane');

        $latestDraw = PrizeDraw::where('draw_date', '>=', $nowBrisbane)
        ->where('winner_user_finalised', 'no')
        ->where('show_on_site', true)
        ->whereNull('winner_id')
        ->orderBy('draw_date', 'asc')
        ->orderBy('created_at', 'desc')
        ->first();

        if ($latestDraw) {
            $countdownEndDate = $latestDraw->draw_date->toIso8601String();
        }

    // Images
        $imageFolder = public_path('images');
        $images = [];

        if (file_exists($imageFolder)) {
            foreach (scandir($imageFolder) as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['png','jpg','jpeg','webp','svg'])) {
                    $images[strtolower($file)] = asset('images/' . $file);
                }
            }
        }

        return compact(
            'plans',
            'subscription',
            'package',
            'metaData',
            'tabData',
            'latestDraw',
            'countdownEndDate',
            'images'
        );
    }


    // public function landingpage()
    // {
    //     return view(
    //         'consumer.landingpage',
    //         $this->buildPageData(2)
    //     );
    // }

    public function landingpage()
    {
        // return view(
        //     'consumer.landingpage',
        //     array_merge(
        //         $this->buildPageData(2),
        //         ['bodyClass' => 'landing-page']
        //     )
        // );
        $data = $this->buildPageData(2);
        $data['showLandingBar'] = true; 
        $data['bodyClass'] = '';
        return view('consumer.landingpage', $data);
    }

    public function mazdapage()
    {
        return view(
            'consumer.mazdapage',
            $this->buildPageData(3)
        );
    }

    public function grangepage()
    {
        return view(
            'consumer.grangepage',
            $this->buildPageData(4)
        );
    }



    public function teampackages()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view packages.');
        }

        $allowedUserIds = ['c2a46676-12fb-4541-a5a8-7c352a3d3694', 'db2891e5-23d9-4240-aae0-2c552e33eda1', '4c9388df-70b3-4cb5-99b6-98b21be97d5d', 'a67102b2-a766-4c76-9ea6-91ca4d91a087', '8f4e874b-6809-4b96-aa62-24ee5203ed4d'];
        if (!in_array(auth()->id(), $allowedUserIds)) {
            abort(403, 'You do not have permission to access these team packages.');
        }

        $plans =Plan::orderBy('position')->where('is_public', true)->get();

        $ids = [26];

        $plans = Plan::whereIn('id', $ids)
        ->orderBy('position')
        ->get();



        return view('consumer.team-packages', compact('plans'));

    } 





    public function packages()
    {

       

        $data = $this->buildPageData(2);
        $data['showLandingBar'] = true; 
        $data['bodyClass'] = 'page-landing-page';
        return view('consumer.landingpage', $data);


        $plans =Plan::orderBy('position')->where('is_public', true)->get();
        $subscription =Plan::where('is_public', true)->where('type', 'subscription')->get();
        $package =Plan::where('is_public', true)->where('type', 'package')->get();
        $page = Page::findOrFail(1);
        $metaData = $page->meta->pluck('meta_value', 'meta_key')->toArray();
        $tabs = ['work', 'rewards', 'community', 'lifestyle'];
        $tabData = [];
        foreach ($tabs as $tab) {
            $tabData[$tab] = [
                'title_line1' => $metaData["{$tab}_tab_title_line1"] ?? '',
                'title_line2' => $metaData["{$tab}_tab_title_line2"] ?? '',
                'paragraph'   => $metaData["{$tab}_tab_paragraph"] ?? '',
                'image'       => $metaData["{$tab}_tab_image"] ?? '',
            ];
        }
        $countdownEndDate= null;
        $nowBrisbane = Carbon::now('Australia/Brisbane');
        $latestDraw = PrizeDraw::where('draw_date', '>=', $nowBrisbane)
        ->where('winner_user_finalised', 'no')
        ->where('show_on_site', true)
        ->whereNull('winner_id')
        ->orderBy('draw_date', 'asc')
        ->orderBy('created_at', 'desc')
        ->first();

        if ($latestDraw) {
            $countdownEndDate = $latestDraw->draw_date;
        }

        return view('consumer.packages', compact('subscription','package','plans', 'metaData','latestDraw','countdownEndDate'));
    }

    public function testportal()
    {
        $page = Page::findOrFail(1);
        $metaData = $page->meta->pluck('meta_value', 'meta_key')->toArray();

        return view('consumer.testportal',compact('metaData'));
    }

    public function thank_you()
    {
        return view('auth.thank-you');
    }

    public function thankyousubscription()
    {
        return view('auth.thank-you');
    }
    public function thankyougold()
    {
        return view('auth.thank-you');
    }
    public function thankyousilver()
    {
        return view('auth.thank-you');
    }
    public function thankyoubronze()
    {
        return view('auth.thank-you');
    }




      public function about_us()
    {
        $plans =Plan::where('is_public', true)->get();
        $subscription =Plan::where('is_public', true)->where('type', 'subscription')->get();
        $package =Plan::where('is_public', true)->where('type', 'package')->get();
        return view('consumer.about_us', compact('subscription','package','plans'));
    }


    public function partners(Request $request)
    {

     return view('consumer.partners');

 }

 public function businessPortal(Request $request)
 {

     return view('consumer.business-portal');

 }

  public function privacyPolicy(Request $request)
 {

     return view('consumer.privacy-policy');

 }



 public function allcategory(Request $request)
 {

    $perpage=6;
    $plans =Plan::where('is_public', true)->get();
    $organisations = Organisation::where('processing_status', OrganisationStatus::APPROVED)->get();
   // $categories = Category::orderBy('position')->get();

    // $categories = Category::orderBy('position')
    //     ->with(['offers' => function($query) {
    //         $query->where('end_date', '>=', Carbon::now());
    //     }, 'offers.attachments']) 
    //     ->get();


    $today = Carbon::now();
    // Eager load offers and their organizations.
    // $categories = Category::with([
    //     'offers' => function ($query) use ($today) {
    //         $query->where('start_date', '<=', $today)
    //         ->where('end_date', '>=', $today)
    //         ->where('processing_status', OrganisationStatus::APPROVED)
    //         ->with('organisation');
    //     }
    // ])
    // ->whereHas('offers', function ($query) use ($today) {
    //     $query->where('start_date', '<=', $today)
    //     ->where('end_date', '>=', $today)
    //     ->where('processing_status', OrganisationStatus::APPROVED);
    // })
    // ->get();

    $categories = Category::with([
        'offers' => function ($query) use ($today) {
            $query->where('start_date', '<=', $today)
            ->where('processing_status', OrganisationStatus::APPROVED)
            ->where(function($query) use ($today) {
                $query->where('end_date', '>=', $today)
                ->orWhere('no_end_date', 1); 
            })
            ->with('organisation');
        }
    ])
    ->orderBy('name')
    ->get();

    $offers = Offer::with(['organisation', 'categories'])->paginate($perpage);
    return view('consumer.category', compact('plans', 'offers', 'organisations', 'categories'));
}





/**
     * Display a listing of the offers with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
*/



public function discounts(Request $request)
{

  // Initialize the popupcheck and business_id variables
    $popupcheck = false;
    $business_id = null;
    $business_cat = false; 
    if (auth()->check() && $request->filled('busness')) {
        $popupcheck = true;
        $business_id = $request->input('busness'); 
    }else{
        $business_id = $request->input('busness'); 
    }

    $today = Carbon::now();
    $searchTerm = request('search'); 
    $categories = Category::with([
        'offers' => function ($query) use ($today, $searchTerm) {
            $query->where('start_date', '<=', $today)
            ->where('processing_status', OrganisationStatus::APPROVED)
            ->where(function($query) use ($today) {
                $query->where('end_date', '>=', $today)
                ->orWhere('no_end_date', 1); 
            })
            ->with('organisation'); 
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    //$q->where('title', 'like', '%' . $searchTerm . '%')
                    $q->WhereHas('organisation', function ($orgQuery) use ($searchTerm) {
                      $orgQuery->where('title', 'like', '%' . $searchTerm . '%');
                  });
                });
            }
        }
    ])
    ->orderBy('name')
    ->get();

    return view('consumer.discounts', compact('popupcheck','business_id', 'categories'));

}

public function shop(Request $request)
{
    $popupcheck = false;
    $business_id = null;

    if (auth()->check() && $request->filled('busness')) {
        $popupcheck = true;
        $business_id = $request->input('busness');
    }

    $today = Carbon::now();
    $searchTerm = $request->search;
    $categories = Category::all();

    // ---------------------------------------------------------
    // 1. OFFER FILTERS (active + approved + category + featured etc.)
    // ---------------------------------------------------------
    $offerFilters = function ($q) use ($today, $request) {
        $q->where('start_date', '<=', $today)
          ->where('processing_status', OrganisationStatus::APPROVED)
          ->where(function ($end) use ($today) {
              $end->where('end_date', '>=', $today)
                  ->orWhere('no_end_date', 1);
          });

        // Category filter
        if ($request->filled('categories')) {
            $q->whereHas('categories', function ($cq) use ($request) {
                $cq->whereIn('categories.id', (array)$request->categories);
            });
        }

        // State filter
        if ($request->filled('state')) {
            $q->whereIn('state', (array)$request->state);
        }

        // Address type filter
        if ($request->filled('offer_address_type')) {
            $q->whereIn('offer_address_type', (array)$request->offer_address_type);
        }

        // Featured filter
        if ($request->filled('is_featured')) {
            $q->whereIn('is_featured', (array)$request->is_featured);
        }
    };

    // ---------------------------------------------------------
    // 2. BASE QUERY FOR ORGANISATIONS
    // ---------------------------------------------------------
    $query = Organisation::query();

    // ---------------------------------------------------------
    // 3. SEARCH LOGIC (ONLY applied when user searches)
    //    This ensures WRONG businesses do NOT appear
    // ---------------------------------------------------------
    $query->whereHas('offers', $offerFilters);
    // 4. SEARCH LOGIC (Nested inside the category constraints)
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm, $offerFilters) {
            // A) Business name matches search
            $q->where('title', 'like', "%{$searchTerm}%");
        });
    }

    // ---------------------------------------------------------
    // 5. EAGER LOAD OFFERS (apply same filters)
    // ---------------------------------------------------------
    $query->with(['offers' => function ($q) use ($offerFilters) {
        $offerFilters($q);
        $q->with('categories', 'attachments');
    }]);

    // ---------------------------------------------------------
    // 6. PAGINATION
    // ---------------------------------------------------------
    $perPage = 12;
    $page = $request->page ?? 1;

    $organizations = $query->paginate($perPage, ['*'], 'page', $page);

    $hasMore = $organizations->hasMorePages();
    $offerclass = 'offer-detail';
    $organizationsToShowInitially = $perPage;


        // DEBUG OUTPUT - What matched and Why
$debugResults = [];

$debugResults['search_term'] = $searchTerm ?? null;

foreach ($organizations as $org) {

    $orgReason = [];

    // 🔹 Did Organisation title match?
    if ($searchTerm && stripos($org->title, $searchTerm) !== false) {
        $orgReason[] = "Organisation title matched search";
    }

    $offersMatched = [];

    foreach ($org->offers as $offer) {

        $offerMatchReason = [];

        // 🔹 Offer title match
        if ($searchTerm && stripos($offer->title, $searchTerm) !== false) {
            $offerMatchReason[] = "Offer title matched search";
        }

        // 🔹 Category match
        if ($request->filled('categories')) {
            foreach ($offer->categories as $cat) {
                if (in_array($cat->id, (array)$request->categories)) {
                    $offerMatchReason[] = "Category filter matched (" . $cat->name . ")";
                }
            }
        }

        // 🔹 State match
        if ($request->filled('state') && in_array($offer->state, (array)$request->state)) {
            $offerMatchReason[] = "State filter matched (" . $offer->state . ")";
        }

        // 🔹 Address Type match
        if ($request->filled('offer_address_type') && in_array($offer->offer_address_type, (array)$request->offer_address_type)) {
            $offerMatchReason[] = "Offer address type matched (" . $offer->offer_address_type . ")";
        }

        // 🔹 Featured match
        if ($request->filled('is_featured') && in_array($offer->is_featured, (array)$request->is_featured)) {
            $offerMatchReason[] = "Featured filter matched";
        }

        if (!empty($offerMatchReason)) {
            $offersMatched[$offer->title] = $offerMatchReason;
        }
    }

    $debugResults['results'][] = [
        'organization' => $org->title,
        'reason_for_inclusion' => $orgReason ?: ['Passed mandatory offer filters'],
        'offers_matched' => $offersMatched
    ];
}

// echo "<pre>";
// print_r($debugResults);
// echo "</pre>";
// exit();


    // ---------------------------------------------------------
    // 7. AJAX RESPONSE
    // ---------------------------------------------------------
    if ($request->ajax()) {
        $html = view('consumer.partials.organisations-list', compact(
            'organizations', 'offerclass', 'organizationsToShowInitially'
        ))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore
        ]);
    }

    // ---------------------------------------------------------
    // 8. NORMAL PAGE LOAD
    // ---------------------------------------------------------
    return view('consumer.shop', compact(
        'popupcheck',
        'business_id',
        'categories',
        'organizations',
        'offerclass',
        'organizationsToShowInitially',
        'hasMore'
    ));
}





public function shopfilter_bk(Request $request)
{
    $popupcheck = false;
        $business_id = null;
        
        if (auth()->check() && $request->filled('busness')) {
            $popupcheck = true;
            $business_id = $request->input('busness'); 
        }
        
        $today = Carbon::now();
        $searchTerm = $request->search; 
        $categories = Category::all();

        // --- START: Reusable Offer Constraints Closure (Filters what to SELECT and what to LOAD) ---
        
        $offerConstraints = function ($q) use ($today, $searchTerm, $request) {
            // 1. Status and Date Constraints (REQUIRED for active offers)
            $q->where('start_date', '<=', $today)
              ->where('processing_status', OrganisationStatus::APPROVED)
              ->where(function($q2) use ($today) {
                  $q2->where('end_date', '>=', $today)
                     ->orWhere('no_end_date', 1);
              });

            // // 2. Offer Search Term Constraint (Applies OR logic across offer title/description)
            // if ($searchTerm) {
            //     $q->where(function($searchQ) use ($searchTerm) {
            //         $searchQ->where('title', 'like', '%' . $searchTerm . '%')
            //                 ->orWhere('description', 'like', '%' . $searchTerm . '%');
            //     });
            // }

              // 2. Offer Search Term Constraint (Applies ONLY to offer title)
              if ($searchTerm) {
                $q->where(function($searchQ) use ($searchTerm) {
        // --- MODIFIED LINE: Only search the 'title' column ---
                    $searchQ->where('title', 'like', '%' . $searchTerm . '%');
        // Removed: ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }
            
            // 3. Category Filter
            if ($request->filled('categories')) {
                $q->whereHas('categories', function ($cq) use ($request) {
                    $cq->whereIn('categories.id', (array)$request->categories);
                });
            }

            // 4. State Filter
            if ($request->filled('state')) {
                $q->whereIn('state', (array)$request->state);
            }

            // 5. Offer Address Type Filter
            if ($request->filled('offer_address_type')) {
                $q->whereIn('offer_address_type', (array)$request->offer_address_type);
            }

            // 6. Featured Filter
            if ($request->filled('is_featured')) {
                $q->whereIn('is_featured', (array)$request->is_featured);
            }
        };
        
        // --- END: Reusable Offer Constraints Closure ---

        
        // Initialize the base query
        $query = Organisation::query();

        // // --- FIX: Implement OR logic for searching Organization Title vs. Offer Content ---
        // if ($searchTerm) {
        //     // Condition A: Organization's own title matches the search term
        //     $query->where('title', 'like', '%' . $searchTerm . '%'); 
        // }
        
        // // Condition B: The Organization has an offer that matches ALL constraints (OR with Condition A)
        // // Note: If $searchTerm is present, this creates an OR, otherwise it's a regular WHERE
        // // The whereHas closure (offerConstraints) handles the AND logic for its own filters.
        // if ($searchTerm) {
        //     $query->orWhereHas('offers', $offerConstraints);
        // } else {
        //     // If no search term, we use a standard whereHas to apply only the general filters (category, state, etc.)
        //     $query->whereHas('offers', $offerConstraints);
        // }


        // --- 1. Enforce Mandatory Filters (Non-Search Filters) ---
// This ensures that ALL organizations returned satisfy the state/category/featured filters
// regardless of the search term.
$query->whereHas('offers', $offerConstraints);


// --- 2. Add Search Term Filter (Organisation Title OR Offer Content) ---
if ($searchTerm) {
    // We wrap the search logic (Org Title OR Offer Content) in a closure.
    // This closure acts as an optional filter AND-ed to the mandatory whereHas above.
    $query->where(function ($q) use ($searchTerm, $offerConstraints) {
        
        // Condition A: Organization's own title matches the search term
        $q->where('title', 'like', '%' . $searchTerm . '%');

        // Condition B: The Organization has an offer that matches the constraints (including search term)
        $q->orWhereHas('offers', $offerConstraints);
    });
}


        
        // Step 2: Eager Load and Filter Offers using with
        $query->with(['offers' => function ($query) use ($offerConstraints) {
            
            // Apply all constraints defined above to the eager-loaded offers
            $offerConstraints($query); 
            
            // Add eager loading for nested relationships
            $query->with('categories', 'attachments');

        }]);


        $perPage = 6;
        $page = $request->page ?? 1;

        $organizations = $query->paginate($perPage, ['*'], 'page', $page);

        // --- START: Outputting Organization Title and Offer Titles ---
        
        $organizationsAndOffers = [];
            
        foreach ($organizations as $organization) {
            
            $organizationName = $organization->title ?? 'Unknown Organization ID: ' . $organization->id; 
            
            $currentOrganizationOffers = [];

            if ($organization->relationLoaded('offers') && $organization->offers->isNotEmpty()) {
                foreach ($organization->offers as $offer) {
                    if (isset($offer->title)) {
                        $currentOrganizationOffers[] = $offer->title;
                    }
                }
            }

             $organizationsAndOffers[$organizationName] = $currentOrganizationOffers;
            
            // if (!empty($currentOrganizationOffers)) {
            //     $organizationsAndOffers[$organizationName] = $currentOrganizationOffers;
            // }
        }

        // echo "<pre>";
        // print_r($organizationsAndOffers);
        // echo "</pre>";
        // exit(); 



    $offerclass = 'offer-detail';
    $organizationsToShowInitially = $perPage;
    $hasMore = $organizations->hasMorePages();

    // Return AJAX response
    if ($request->ajax()) {
        $html = view('consumer.partials.organisations-list', compact(
            'organizations', 'offerclass', 'organizationsToShowInitially'
        ))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore
        ]);
    }

    // Normal page load
    return view('consumer.shopfilter', compact(
        'popupcheck',
        'business_id',
        'categories',
        'organizations',
        'offerclass',
        'organizationsToShowInitially',
        'hasMore'
    ));
}





public function showofferModalDetails(Request $request, Offer $offer)
{
    if ($request->query('check_only_auth')) {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->two_factor_code){
                return response()->json(['logged_in' => false]);
            }
            return response()->json(['logged_in' => true]);
        } else {
           return response()->json(['logged_in' => false]);
       }
   }

   $offer->load(['organisation', 'attachments', 'categories']);
   return response()->view('consumer.partials.modal_offer_details', compact('offer'));
}



public function businessdetails(string $business_id)
{

    $organisation = Organisation::findOrFail($business_id);

    if (Auth::check()) {
        $user = Auth::user();
        if ($user->two_factor_code) {
            return redirect()->route('verify.index');
        }
    } else {
        return redirect()->route('login');
    }


    $organisation->load(['offers.attachments', 'offers.categories']);
    $organisation->load(['offers' => function ($query) {
        $today = \Carbon\Carbon::now();
        $query->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED);
    }, 'offers.attachments', 'offers.categories']);

    $userId = auth()->id(); 
    $hasActiveSubscription = UserPackageSubscription::where('user_id', $userId)->where('status', 'active')->exists();

    return response()->view('consumer.businessdetails', compact('organisation','hasActiveSubscription'));

}




// public function showModalDetails(Request $request, Organisation $organisation)
// {
//     if ($request->query('check_only_auth')) {
//         if (Auth::check()) {
//             $user = Auth::user();
//             if ($user->two_factor_code){
//                 return response()->json(['logged_in' => false]);
//             }
//             return response()->json(['logged_in' => true]);
//         } else {
//            return response()->json(['logged_in' => false]);
//        }
//    }

//    $organisation->load(['offers.attachments', 'offers.categories']);
//    $organisation->load(['offers' => function ($query) {
//     $today = \Carbon\Carbon::now();
//     $query->where('start_date', '<=', $today)
//     ->where('end_date', '>=', $today)
//     ->where('processing_status', OrganisationStatus::APPROVED);
// }, 'offers.attachments', 'offers.categories']);

//    $userId = auth()->id(); 
//    $hasActiveSubscription = UserPackageSubscription::where('user_id', $userId)->where('status', 'active')->exists();

//    return response()->view('consumer.partials.modal_organisation_details', compact('organisation','hasActiveSubscription'));

// }



public function redeemoffersite(Request $request, Offer $offer)
{
    $user = $request->user();
    $userIdentifier = $user->email ?? $user->phone ?? $user->id;

    // Check if already logged
    $activityExists = \DB::table('user_activity_logs')
        ->where('user_id', $user->id)
        ->where('event', 'offer_redeem_visit')
        ->where('typeid', $offer->id)
        ->exists();

    if (!$activityExists) {
        logUserActivity(
            $user->id,
            'offer_redeem_visit',
            sprintf(
                'User %s viewed the offer "%s" (Offer ID: %s) on %s',
                $userIdentifier,
                $offer->title,
                $offer->id,
                now()->toDateTimeString()
            ),
            [
                'user_identifier' => $userIdentifier,
                'offer_id'        => $offer->id,
                'offer_title'     => $offer->title,
                'timestamp'       => now()->toDateTimeString()
            ],
            'offer',
            $offer->id
        );
    }

    return true;
}


public function showModalDetails(Request $request, Organisation $organisation)
{
    // Check authentication only
    if ($request->query('check_only_auth')) {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->two_factor_code){
                return response()->json(['logged_in' => false]);
            }
            return response()->json(['logged_in' => true]);
        } else {
           return response()->json(['logged_in' => false]);
       }
   }



/****************** Dynamically generate and update missing custom promo codes. **********************/

   $offers = $organisation->offers()->whereNull('custom_redemption_code')->orWhere('custom_redemption_code', '')->get();
   foreach ($offers as $offer) {
     $uniqueCode = Str::upper(Str::random(12)); 
     $offer->custom_redemption_code = $uniqueCode;
     $offer->save();
 }

 /****************************************/


   // Get offer_ids from request if passed
   $offerIds = $request->query('offer_ids') 
       ? explode(',', $request->query('offer_ids')) 
       : [];

   // Load offers, optionally filter by offer_ids
   $organisation->load(['offers' => function ($query) use ($offerIds) {
       $today = \Carbon\Carbon::now();
       $query->where('start_date', '<=', $today)
           // ->where('end_date', '>=', $today)
           // ->where('processing_status', OrganisationStatus::APPROVED);

       ->where('processing_status', OrganisationStatus::APPROVED)
        ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        });

       if (!empty($offerIds)) {
           $query->whereIn('id', $offerIds);
       }
   }, 'offers.attachments', 'offers.categories']);

   $userId = auth()->id(); 
   $hasActiveSubscription = UserPackageSubscription::where('user_id', $userId)
       ->where('status', 'active')->exists();

   return response()->view('consumer.partials.modal_organisation_details', compact('organisation','hasActiveSubscription'));
}





public function logincheckredirection(Request $request, Offer $offer)
{
    if ($request->query('check_only_auth')) {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->two_factor_code){
                return response()->json(['logged_in' => false]);
            }
            return response()->json(['logged_in' => true]);
        } else {
           return response()->json(['logged_in' => false]);
       }
   }

   $offer->load(['organisation', 'attachments', 'categories']);
   return response()->view('consumer.partials.modal_offer_details', compact('offer'));
}







public function ajaxSubscribe(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $mailchimp = new MailChimp(env('MAILCHIMP_APIKEY'));
    $list_id = env('MAILCHIMP_LIST_ID');
    $email = strtolower($request->email);
    $subscriberHash = md5($email);
    $result = $mailchimp->put("lists/$list_id/members/$subscriberHash", [
        'email_address' => $email,
        'status_if_new' => 'subscribed',
    ]);

    if (isset($result['status'])) {
        if ($result['status'] === 'subscribed') {
            return response()->json(['success' => true, 'message' => 'Successfully subscribed.', 'result' => $result]);
        } elseif ($result['status'] === 'pending') {
            return response()->json(['success' => true, 'message' => 'Subscription pending. Please check your email.', 'result' => $result]);
        } elseif ($result['status'] === 'archived') {
        // This is the specific case for an archived member
            return response()->json(['success' => false, 'message' => 'This email address is no longer on our list.', 'result' => $result]);
        }
    }

    return response()->json(['success' => false, 'message' => $result['detail'] ?? 'Subscription failed.', 'result' => $result]);
}


public function getLists()
{
    $mailchimp = new MailChimp(env('MAILCHIMP_APIKEY'));
    $listId = env('MAILCHIMP_LIST_ID');

    // Get all subscribed members (status = subscribed)
    $response = $mailchimp->get("lists/{$listId}/members", [
        'status' => 'subscribed', // you can also use 'unsubscribed', 'cleaned', 'pending'
        'count' => 10 // max per page
    ]);

    if (!empty($response['members'])) {
        foreach ($response['members'] as $member) {
            echo 'Email: ' . $member['email_address'] . '<br>';
            echo 'Status: ' . $member['status'] . '<br>';
            echo 'FNAME: ' . ($member['merge_fields']['FNAME'] ?? '-') . '<br>';
            echo 'LNAME: ' . ($member['merge_fields']['LNAME'] ?? '-') . '<br><hr>';
        }
    } else {
        dd('No subscribers found or error:', $response);
    }
}


public function getSubscribers()
{
    $mailchimp = new MailChimp(env('MAILCHIMP_APIKEY'));
    $listId = env('MAILCHIMP_LIST_ID');

    // Get all subscribed members (status = subscribed)
    $response = $mailchimp->get("lists/{$listId}/members", [
        'status' => 'subscribed', // you can also use 'unsubscribed', 'cleaned', 'pending'
        'count' => 10 // max per page
    ]);

    if (!empty($response['members'])) {
        foreach ($response['members'] as $member) {
            echo 'Email: ' . $member['email_address'] . '<br>';
            echo 'Status: ' . $member['status'] . '<br>';
            echo 'FNAME: ' . ($member['merge_fields']['FNAME'] ?? '-') . '<br>';
            echo 'LNAME: ' . ($member['merge_fields']['LNAME'] ?? '-') . '<br><hr>';
        }
    } else {
        dd('No subscribers found or error:', $response);
    }
}



public function events_old(Request $request)
{
    $perPage = 6;
    $offset = $request->input('offset', 0);
    $searchQuery = $request->input('s');

    // ==========================
    // ✅ DATE CONDITION
    // ==========================
    $dateCondition = function ($q) {
        $now = \Carbon\Carbon::now();
        $today = \Carbon\Carbon::today();
        $endOfToday = \Carbon\Carbon::today()->endOfDay();

        $q->where(function ($q2) use ($now, $today, $endOfToday) {
            // Case 1: Events that have both start & end
            $q2->whereNotNull('end')
               ->where(function ($qq) use ($now, $today, $endOfToday) {
                   $qq->whereBetween('start', [$today, $endOfToday]) // starts today
                      ->orWhereBetween('end', [$today, $endOfToday])   // ends today
                      ->orWhere(function ($q3) use ($now) {
                          $q3->where('start', '<=', $now)
                             ->where('end', '>=', $now); // ongoing now
                      });
               });
        })
        ->orWhere(function ($q3) use ($today) {
            // Case 2: Only start date (no end)
            $q3->whereNull('end')
               ->whereDate('start', '>=', $today);
        })
        ->orWhere('start', '>=', \Carbon\Carbon::now()); // Case 3: Future events
    };

    // ==========================
    // ✅ AJAX REQUEST
    // ==========================
    if ($request->ajax()) {
        $categoryId = $request->input('category_id');

        $baseQueryForCategory = Events::where('event_category', $categoryId)
            ->where($dateCondition)
            ->orderBy('start', 'asc');

        if ($searchQuery) {
            $baseQueryForCategory->where('name', 'like', '%' . $searchQuery . '%');
        }

        $events_data = (clone $baseQueryForCategory)
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Group by YEAR → MONTH (based on start date)
        $events = $events_data->groupBy(function ($event) {
            return \Carbon\Carbon::parse($event->start)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($event) {
                return \Carbon\Carbon::parse($event->start)->format('F Y');
            });
        });

        $totalEventsCount = (clone $baseQueryForCategory)->count();
        $hasMore = ($offset + $perPage) < $totalEventsCount;

        $monthYearsArray = [];
        foreach ($events as $year => $months) {
            foreach ($months as $monthYear => $eventsInMonth) {
                $sluggedMonthYear = $categoryId . '-' . \Illuminate\Support\Str::slug($monthYear, '-');
                $monthYearsArray[] = $sluggedMonthYear;
            }
        }

        $event_category = $categoryId;
        $html = view('consumer.partials.events-list', compact('events', 'event_category'))->render();

        return response()->json([
            'html' => $html,
            'monthYearsArray' => $monthYearsArray,
            'hasMore' => $hasMore,
            'perPage' => $perPage,
            'searchQuery' => $searchQuery,
            'event_category' => $categoryId,
        ]);
    }

    // ==========================
    // ✅ CASH EVENTS
    // ==========================
    $cashEventsBaseQuery = Events::where('event_category', 1)
        ->where($dateCondition)
        ->orderBy('start', 'asc');

    if ($searchQuery) {
        $cashEventsBaseQuery->where('name', 'like', '%' . $searchQuery . '%');
    }

    $cash_events_data = (clone $cashEventsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();

    $cash_events = $cash_events_data->groupBy(function ($event) {
        return \Carbon\Carbon::parse($event->start)->format('Y');
    })->map(function ($yearGroup) {
        return $yearGroup->groupBy(function ($event) {
            return \Carbon\Carbon::parse($event->start)->format('F Y');
        });
    });

    $cash_totalEventsCount = (clone $cashEventsBaseQuery)->count();
    $cash_hasMore = ($offset + $perPage) < $cash_totalEventsCount;

    // ==========================
    // ✅ PRIZE EVENTS
    // ==========================
    $prizeEventsBaseQuery = Events::where('event_category', 2)
        ->where($dateCondition)
        ->orderBy('start', 'asc');

    if ($searchQuery) {
        $prizeEventsBaseQuery->where('name', 'like', '%' . $searchQuery . '%');
    }

    $prize_events_data = (clone $prizeEventsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();

    $prize_events = $prize_events_data->groupBy(function ($event) {
        return \Carbon\Carbon::parse($event->start)->format('Y');
    })->map(function ($yearGroup) {
        return $yearGroup->groupBy(function ($event) {
            return \Carbon\Carbon::parse($event->start)->format('F Y');
        });
    });

    $prize_totalEventsCount = (clone $prizeEventsBaseQuery)->count();
    $prize_hasMore = ($offset + $perPage) < $prize_totalEventsCount;

    // ==========================
    // ✅ CHARITY EVENTS
    // ==========================
    $charityEventsBaseQuery = Events::where('event_category', 3)
        ->where($dateCondition)
        ->orderBy('start', 'asc');

    if ($searchQuery) {
        $charityEventsBaseQuery->where('name', 'like', '%' . $searchQuery . '%');
    }

    $charity_events_data = (clone $charityEventsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();

    $charity_events = $charity_events_data->groupBy(function ($event) {
        return \Carbon\Carbon::parse($event->start)->format('Y');
    })->map(function ($yearGroup) {
        return $yearGroup->groupBy(function ($event) {
            return \Carbon\Carbon::parse($event->start)->format('F Y');
        });
    });

    $charity_totalEventsCount = (clone $charityEventsBaseQuery)->count();
    $charity_hasMore = ($offset + $perPage) < $charity_totalEventsCount;

    // ==========================
    // ✅ RETURN VIEW
    // ==========================
    return view('consumer.events', compact(
        'cash_events', 'cash_hasMore',
        'prize_events', 'prize_hasMore',
        'charity_events', 'charity_hasMore',
        'perPage', 'searchQuery'
    ));
}



public function events(Request $request)
{
    $perPage = 60;
    $offset = $request->input('offset', 0);
    $searchQuery = $request->input('s');

    // Base query: all prize draws, ordered by draw date
    $drawsBaseQuery = PrizeDraw::where('draw_date', '>=', now())
        ->where('winner_user_finalised', 'no')
        ->where('show_on_site', 1) 
        ->orderBy('draw_date', 'asc');

    // Apply search filter if present
    if ($searchQuery) {
        $drawsBaseQuery->where('title', 'like', '%' . $searchQuery . '%');
    }

    // Optional pagination
    $draws_data = (clone $drawsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();

    // Separate variables for cash and non_cash
    $cash_events = $draws_data->where('prize_type', 'cash')
        ->groupBy(function ($draw) {
            return Carbon::parse($draw->draw_date)->format('Y'); // Year
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($draw) {
                return Carbon::parse($draw->draw_date)->format('F Y'); // Month + Year
            });
        });

    $prize_events = $draws_data->where('prize_type', 'non_cash')
        ->groupBy(function ($draw) {
            return Carbon::parse($draw->draw_date)->format('Y'); // Year
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($draw) {
                return Carbon::parse($draw->draw_date)->format('F Y'); // Month + Year
            });
        });



        $dateCondition = function ($q) {
            $now = \Carbon\Carbon::now();
            $today = \Carbon\Carbon::today();
            $endOfToday = \Carbon\Carbon::today()->endOfDay();

            $q->where(function ($q2) use ($now, $today, $endOfToday) {
                $q2->whereNotNull('end')
                ->where(function ($qq) use ($now, $today, $endOfToday) {
                 $qq->whereBetween('start', [$today, $endOfToday]) 
                 ->orWhereBetween('end', [$today, $endOfToday])   
                 ->orWhere(function ($q3) use ($now) {
                  $q3->where('start', '<=', $now)
                  ->where('end', '>=', $now); 
              });
             });
            })
            ->orWhere(function ($q3) use ($today) {
            // Case 2: Only start date (no end)
                $q3->whereNull('end')
                ->whereDate('start', '>=', $today);
            })
            ->orWhere('start', '>=', \Carbon\Carbon::now()); 
        };


        $charityEventsBaseQuery = Events::where($dateCondition)
        ->orderBy('start', 'asc');

        if ($searchQuery) {
            $charityEventsBaseQuery->where('name', 'like', '%' . $searchQuery . '%');
        }

        $charity_events_data = (clone $charityEventsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();

        $charity_events = $charity_events_data->groupBy(function ($event) {
            return \Carbon\Carbon::parse($event->start)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($event) {
                return \Carbon\Carbon::parse($event->start)->format('F Y');
            });
        });

       



    // ==========================
    // ✅ RETURN VIEW
    // ==========================
    return view('consumer.events', compact(
        'cash_events',
        'prize_events',
        'charity_events',
        'perPage',
        'searchQuery'
    ));
}



public function supportandlegal(Request $request)
{
 $faqs = Faq::orderBy('position')->get();
 return view('consumer.support-and-legal', compact('faqs'));

}

public function termsandconditions(Request $request)
{
 $faqs = Faq::orderBy('position')->get();
 return view('consumer.terms-and-conditions', compact('faqs'));

}


public function contactus(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phonenumber' => 'required|string|max:20',
        'msg' => 'required|string|max:1000',
    ]);
    
    try {

        $to_email = env('ADMIN_CONTACT_EMAIL');
        $recipients = [
            $to_email,
            'info@xhale.com.au',
        ];

        $emailData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phonenumber' => $validatedData['phonenumber'],
            'msg' => $validatedData['msg'],
        ];

        Mail::send('emails.contact_form', $emailData, function($message) use ($emailData, $recipients) {
            $message->to($recipients);
            $message->subject('New Contact Form Submission from ' . $emailData['name']);
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->replyTo($emailData['email'], $emailData['name']);
        });

        return response()->json(['message' => 'Your message has been sent successfully! We will contact you soon!'], 200);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to send your message. Please try again later.'], 500);
    }
}




public function createbusinessaccount(Request $request)
{

 $user = User::create([
    'name' => $request->first_name.' '.$request->last_name,
    'first_name' => $request->first_name,
    'last_name' => $request->last_name,
    'email' => $request->email,
    'password' => bcrypt($request->password),
    'mobile' => $request->mobile,
    'level' => 'business'
]);

 $user->notify(new NewBusinessAccountNotification($user));


 $organisationData = [
    'user_id' => $user->id,
    'title' => $request->business_ame,
    'abn' => $request->abn,
    'instagram' => $request->instagram,
    'facebook' => $request->facebook,
    'youtube' => $request->youtube,
    'tiktok' => $request->tiktok,
    'twitter' => $request->twitter,
    'organisation_address_type' => $request->organisation_address_type,
];

if ($request->organisation_address_type === 'physical_location') {
    $organisationData = array_merge($organisationData, [
        'street_name' => $request->business_address_components['street_name'] ?? null,
        'city' => $request->business_address_components['city'] ?? null,
        'state' => $request->business_address_components['state'] ?? null,
        'postcode' => $request->business_address_components['postcode'] ?? null,
        'country' => $request->business_address_components['country'] ?? null,
        'latitude' => $request->business_latitude,
        'longitude' => $request->business_longitude,
        'full_address' => $request->business_address_search,
    ]);
} else {
    $organisationData = array_merge($organisationData, [
        'street_name' => null,
        'city' => null,
        'state' => null,
        'postcode' => null,
        'country' => null,
        'latitude' => null,
        'longitude' => null,
        'full_address' => null,
    ]);
}

if ($request->hasFile('image')) {
    $newImagePath = $request->file('image')->store('organisations/images', 'public');
    $organisationData['image'] = $newImagePath;
}
$organisation = Organisation::create($organisationData);

$adminEmail = env('ADMIN_CONTACT_EMAIL');
Notification::route('mail', $adminEmail)->notify(new NewOrganisationNotification($organisation));



$offerData = [
    'user_id' => $user->id,
    'title' => $request->title,
    'redemption_process' => 'custom', 
    'custom_redemption_code' => $request->custom_redemption_code,
    'custom_redemption_url' => $request->custom_redemption_url,
    'custom_redemption_description' => $request->custom_redemption_description,
    'full_price' => $request->full_price,
    'discount_price' => $request->discount_price,
    'percentage_off' => $request->percentage_off,
    'custom_saving' => $request->custom_saving,
    'offer_address_type' => $request->offer_address_type,
    'organisation_id' => $organisation->id, 
    'start_date' => now(),
    'end_date' => now()->addMonth(), 
];

if ($request->offer_address_type == 'physical_location') {
    $offerData = array_merge($offerData, [
        'full_address' => $request->offer_address_search,
        'latitude' => $request->offer_latitude,
        'longitude' => $request->offer_longitude,
        'street_name' => $request->offer_address_components['street_name'] ?? null,
        'city' => $request->offer_address_components['city'] ?? null,
        'state' => $request->offer_address_components['state'] ?? null,
        'postcode' => $request->offer_address_components['postcode'] ?? null,
        'country' => $request->offer_address_components['country'] ?? null,
    ]);
} else {
    $offerData = array_merge($offerData, [
        'full_address' => null,
        'latitude' => null,
        'longitude' => null,
        'street_name' => null,
        'city' => null,
        'state' => null,
        'postcode' => null,
        'country' => null,
    ]);
}


$updatedImageFilePaths = []; 
if ($request->hasFile('image_files')) {
    $uploadDirectory = 'offers/image_files';
    foreach ($request->file('image_files') as $file) {
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = $this->getUniqueFileName($originalFileName, $uploadDirectory);
        $path = $file->storeAs($uploadDirectory, $uniqueFileName, 'public');
        $updatedImageFilePaths[] = $path;
    }
    $offerData['image_file'] = $updatedImageFilePaths;
} else {
    $offerData['image_file'] = [];
}

$offer = Offer::create($offerData);

// --- CORRECTED: Only one loop for creating attachments ---
foreach ($updatedImageFilePaths as $filePath) {
    $offer->attachments()->create([
        'file_image' => $filePath,
    ]);
}

Notification::route('mail', $adminEmail)->notify(new NewOfferNotification($offer));


// You likely have a redirect or response here:
// return redirect()->route('some.success.route')->with('success', 'Offer and Organisation created successfully!');


  // Log in the user after successful creation
auth()->login($user);
return redirect()->route('business.myaccount.index')->with('status', 'Thanks for submitting your offer. Our team will review it shortly. You’ll receive an email once your offer is live.');

}



public function check_email(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);
    $exists = User::where('email', $request->email)->exists();
    return response()->json([
        'exists' => $exists,
        'message' => $exists ? 'Email already registered' : 'Email available'
    ]);
}


public function check_mobile(Request $request)
{
        // Validate the incoming request for a required mobile number field
    $validator = Validator::make($request->all(), [
        'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
    ]);

        // If validation fails, return a JSON response with the error
    if ($validator->fails()) {
        return response()->json(['exists' => true, 'message' => 'Invalid mobile number format.']);
    }

        // Check if a user with the given mobile number exists in the database
    $exists = User::where('mobile', $request->mobile)->exists();

        // Return a JSON response with the existence status and a message
    if ($exists) {
        return response()->json(['exists' => true, 'message' => 'This mobile number is already in use.']);
    }

    return response()->json(['exists' => false, 'message' => 'Mobile number is available.']);
}

 public function UnregisteredUsersStore(Request $request)
    {
        $timezone = env('APP_TIMEZONE', 'Australia/Sydney');
        // ---------------------------
        // 1️⃣ Validation
        // ---------------------------
        $validator = Validator::make($request->all(), [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^\+?\d{10,15}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ---------------------------
        // 2️⃣ Save or update unregistered user
        // ---------------------------
        UnregisteredUser::updateOrCreate(
            ['email' => $request->email], // primary key for update
            [
                'name'             => $request->name,
                'mobile'           => $request->mobile,
                'last_activity_at' => now(),
                'followup_step'    => 1, 
                'next_followup_at' => Carbon::now($timezone)->addHours(1), 
            ]
        );

        // ---------------------------
        // 3️⃣ Return JSON response
        // ---------------------------
        return response()->json([
            'status'  => true,
            'message' => 'Unregistered user saved successfully'
        ]);
    }

public function validate_password(Request $request)
{
    $validator = Validator::make($request->all(), [
        'password' => [
            'required',
            'string',
            'min:8',
            'max:32',
            function ($attribute, $value, $fail) {
                if (!preg_match('/[A-Z]/', $value)) {
                    $fail('Password must contain at least one uppercase letter.');
                }
                if (!preg_match('/[a-z]/', $value)) {
                    $fail('Password must contain at least one lowercase letter.');
                }
                if (!preg_match('/[0-9]/', $value)) {
                    $fail('Password must contain at least one number.');
                }
                if (!preg_match('/[\W_]/', $value)) {
                    $fail('Password must contain at least one special character.');
                }
            }
        ]
    ], [
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 8 characters',
        'password.max' => 'Password cannot exceed 32 characters'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'valid' => false,
            'message' => $validator->errors()->first()
        ]);
    }

    return response()->json([
        'valid' => true,
        'message' => 'Password is valid'
    ]);
}



public function members_sigup(Request $request)
{
 $categories = Category::orderBy('position')->get();
 return view('consumer.sigup', compact('categories'));


}



public function optimizeFolder($path)
{
    $files = File::files($path);
    $results = [];

    $optimizer = OptimizerChainFactory::create(); // Create the optimizer once

    foreach ($files as $file) {
        $originalSize = filesize($file->getRealPath());

        $optimizer->optimize($file->getRealPath());

        $newSize = filesize($file->getRealPath());
        $results[] = [
            'file' => $file->getFilename(),
            'original_size' => $this->formatBytes($originalSize),
            'new_size' => $this->formatBytes($newSize),
        ];
    }

    return $results;
}

// Helper function to format bytes nicely
private function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

public function optimizeUploads()
{
   // $path = storage_path('app/public/uploads');
    $path = base_path('public/storage/offers/image_files');

    return $this->optimizeFolder($path);
}


}
