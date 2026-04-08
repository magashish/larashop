<?php

namespace App\Http\Controllers\Subscriber;
use App\Http\Controllers\Controller;

// use Illuminate\Http\Request;
// use App\Models\User;
// use App\Models\Plan; 
// use Illuminate\Support\Facades\Storage;


use App\Enums\OrganisationStatus; 
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Plan; 
use App\Models\Organisation;
use App\Models\Offer;
use App\Models\User;





use App\Models\UserPackageSubscription; // Your custom subscription table model
use Laravel\Cashier\Cashier; // Although not directly used for user methods, good for context
use Stripe\Exception\CardException; // Import Stripe's CardException

use App\Models\Events;
use Carbon\Carbon; // For current date
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Don't forget to import Log
use Illuminate\Support\Facades\Config; // <-- Make sure this is imported!
use Illuminate\Support\Str; 
use App\Models\Faq;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Mail; // Crucial: Import the Mail facade
use Illuminate\Support\Facades\Storage;

use App\Support\AdminNotifiable; 

use App\Notifications\AccountDeleted; 
use App\Notifications\UserAccountDeletedNotification;





use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rules\Password; 


use App\Models\SubscriptionLog;


use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; 
use App\Models\PrizeDrawEntryBackup; 


use Stripe\Stripe; // For direct Stripe API calls (e.g., refunds)
use Stripe\Refund; // For direct Stripe API calls (e.g., refunds)
use Stripe\Invoice; // Import Stripe Invoice for retrieving latest invoice
use App\Notifications\PurchaseConfirmationNotification; // <-- This is the key line

use App\Services\SmsService;
use App\Helpers\PhoneHelper;

class SubscriberDashboardController extends Controller
{


     
    // ================================================================
    // subscription_package — Show page
    // Already has subscription → redirect to dashboard
    // ================================================================
    public function subscription_package(Request $request)
    {
        $user = auth()->user();
        $packagesubscriptions = $user->userPackageSubscriptions;
        if ($packagesubscriptions->isNotEmpty()) {
            return redirect()->route('membership.dashboard')->with('success', 'Login successfully!');
        }

        $subscriptions = UserPackageSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('starts_at', 'desc')
            ->get();

        $plans = Plan::orderBy('position')->where('is_public', true)->get();

        $currentSubscription = UserPackageSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('starts_at', 'desc')
            ->first();

        $currentSubscriptionId = UserPackageSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('starts_at', 'desc')
            ->value('plan_id');

        $intent = auth()->user()->createSetupIntent([
            'payment_method_options' => [
                'card' => ['request_three_d_secure' => 'automatic'],
            ],
        ]);

        $isSubscription = false;

        return view('subscriber.subscription-package ', compact('plans', 'subscriptions', 'user', 'currentSubscription', 'intent', 'isSubscription'));
    }

    // ================================================================
    // subscription_package_store — Process payment
    // Stripe: payment_method present
    // PayPal: payment_method empty → PayPal redirect
    // ================================================================
    public function subscription_package_store(Request $request)
    {
        $user     = $request->user();
        $plan     = Plan::findOrFail($request->selected_plan_id);
        $planType = $plan->type;
        $paymentMethodId = $request->input('payment_method');
         // terms_accepted — dono flows ke liye yahan update karo
        if ($request->has('terms_accepted')) {
            $user->update(['terms_accepted' => 1]);
        }

        // -------------------------------------------------------
        // PAYPAL FLOW — payment_method empty hai
        // -------------------------------------------------------
        if (empty($paymentMethodId)) {

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $currency = strtoupper(env('DEFAULT_CURRENCY', 'AUD'));

            // Session mein plan save karo — success() mein use hoga
            session([
                'paypal_plan_id'   => $plan->id,
                'paypal_type'      => $planType,
                'paypal_is_upsell' => false,
                // Registration flag nahi — logged-in user hai
            ]);

            if ($planType === 'package') {
                // One-time charge
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

                Log::error('PayPal Order Creation Failed (subscription_package_store)', $order);
                return redirect()->back()->with('error', 'PayPal order creation failed. Please try again.');

            } elseif ($planType === 'subscription') {
                // Recurring subscription
                if (empty($plan->paypal_plan_id)) {
                    return redirect()->back()->with('error', 'This plan does not have a PayPal subscription configured.');
                }

                $subscription = $provider->createSubscription([
                    'plan_id' => $plan->paypal_plan_id,
                    'subscriber' => [
                        'name' => [
                            'given_name' => $user->first_name ?? $user->name ?? '',
                            'surname'    => $user->last_name ?? '',
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

                Log::error('PayPal Subscription Creation Failed (subscription_package_store)', $subscription);
                return redirect()->back()->with('error', 'PayPal subscription creation failed. Please try again.');
            }
        }

        // -------------------------------------------------------
        // STRIPE FLOW — original logic unchanged
        // -------------------------------------------------------
        $cardHolderName  = $user->name;
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

                // Payment successful — entries banao
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

            return redirect()->route('subscription.manage')->with('success', $success_msg);

        } catch (\Laravel\Cashier\Exceptions\PaymentActionRequiredException $e) {
            return back()->withErrors(['stripe_error' => 'Payment requires additional authentication. Please try again or use a different payment method.'])->withInput();
        } catch (\Laravel\Cashier\Exceptions\PaymentFailure $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
        } catch (CardException $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred during purchase. Please try again.'])->withInput();
        }
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans =Plan::orderBy('position')->where('is_public', true)->get();
        $offers = Offer::with(['organisation', 'categories'])->get();
        $perPage =6; 
        $offset = 0; 
        $EventsBaseQuery = Events::orderBy('created_at', 'desc')
        ->where('event_category', 1);

        $events_data = (clone $EventsBaseQuery)
        ->skip($offset)
        ->take($perPage)
        ->get();
        $events = $events_data->groupBy(function ($event) {
            return Carbon::parse($event->created_at)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($event) {
                return Carbon::parse($event->created_at)->format('F Y');
            });
        });
        return view('subscriber.dashboard ', compact('plans','offers','events'));
    }





    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $user = auth()->user();
        try {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('image')->store('profile-images', 'public');
            $user->profile_image = $path;
            if (!$user->save()) {
                throw new \Exception('Failed to save user model');
            }
            return response()->json([
                'success' => true,
                'image_url' => Storage::url($user->profile_image)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function membership_dashboard(Request $request)
    {


        $user = auth()->user();
        //$subscriptions = $user->subscriptions()->active()->get();
        $subscriptions = UserPackageSubscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->orderBy('starts_at', 'desc')
        ->get();
        $plans =Plan::orderBy('position')->where('is_public', true)->get();
        $subscription_plans =Plan::where('is_public', true)->where('type', 'subscription')->orderBy('id', 'desc')->get();
        $package_plans      =Plan::where('is_public', true)->where('type', 'package')->orderBy('id', 'desc')->get();
        //$allplans = $package_plans->merge($subscription_plans);
        $allplans =Plan::orderBy('position')->where('is_public', true)->get();



        $currentSubscription = null; 
        $currentSubscription = UserPackageSubscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->orderBy('starts_at', 'desc')
        ->first();
        $currentSubscriptionId = UserPackageSubscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->orderBy('starts_at', 'desc')
        ->value('plan_id'); 

       return view('subscriber.membership ', compact('plans','allplans', 'subscriptions','user','currentSubscription'));
    }



    public function billing_dashboard(Request $request)
    {

    $user = auth()->user();

     try {
        $paymentMethods = $user->paymentMethods();
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'No such customer')) {
            $msg='There seems to be an issue, we apologies for any inconvenience.';

            return view('subscriber.errors-stripe ', compact('user','msg'));
        }
        abort(500, 'Something went wrong while retrieving payment methods.');
    }

    $defaultPaymentMethod = $user->defaultPaymentMethod();
    $plans =Plan::orderBy('position')->where('is_public', true)->get(); 
    return view('subscriber.billing ', compact('user', 'paymentMethods', 'defaultPaymentMethod', 'plans'));
}


    //  public function setDefaultPaymentMethod(Request $request)
    // {
    //     // Add validation rules for the new fields
    //     $request->validate([
    //         'payment_method_id' => 'required|string',
    //         'is_new_card'       => 'required|boolean',
    //         'payment_type'      => 'required|in:card',
    //         // Only require name/email if it's a new card being added
    //         'card_holder_name'  => 'required_if:is_new_card,true|nullable|string|max:255',
    //         'card_holder_email' => 'required_if:is_new_card,true|nullable|email|max:255',
    //     ]);
    //     $user = auth()->user();
    //     $paymentMethodId = $request->input('payment_method_id');
    //     $isNewCard = $request->boolean('is_new_card');
    //     try {
    //         if ($isNewCard) {
    //             $user->addPaymentMethod($paymentMethodId);
    //             $user->updateStripeCustomer([
    //                 'name' => $request->input('card_holder_name'),
    //                 'email' => $request->input('card_holder_email'),
    //             ]);
    //         }
    //         $user->updateDefaultPaymentMethod($paymentMethodId);
    //         return back()->with('success', 'Default payment method updated successfully!');
    //     } catch (\Laravel\Cashier\Exceptions\PaymentActionRequiredException $e) {
    //         return back()->with('error', 'Payment action required to confirm new default method.'.$e->payment->client_secret);
    //     } catch (CardException $e) {
    //         return back()->withErrors(['error' => $e->getMessage()])->withInput();
    //     } catch (\Exception $e) {
    //         return back()->withErrors(['error' => 'An unexpected error occurred. Please try again.'])->withInput();
    //     }
    // }



public function setDefaultPaymentMethod(Request $request)
{
    $request->validate([
        'payment_method_id' => 'required|string',
        'is_new_card'       => 'required|boolean',
        'payment_type'      => 'required|in:card',
        'card_holder_name'  => 'required_if:is_new_card,true|nullable|string|max:255',
        'card_holder_email' => 'required_if:is_new_card,true|nullable|email|max:255',
    ]);

    $user = Auth::user();
    $paymentMethodId = $request->input('payment_method_id');
    $isNewCard       = $request->boolean('is_new_card');

    // Ensure Stripe customer exists
    if (!$user->hasStripeId()) {
        $user->createAsStripeCustomer([
            'name'  => $user->name,
            'email' => $user->email,
        ]);
    }

    if ($isNewCard) {
        // Attach new card
        $user->addPaymentMethod($paymentMethodId);

        // Update customer details if provided
        if ($request->filled('card_holder_name') || $request->filled('card_holder_email')) {
            $user->updateStripeCustomer([
                'name'  => $request->input('card_holder_name', $user->name),
                'email' => $request->input('card_holder_email', $user->email),
            ]);
        }
    }

    // Set as default payment method
    if ($paymentMethodId) {
        $user->updateDefaultPaymentMethod($paymentMethodId);
        return back()->with('success', 'Default payment method updated successfully!');
    } else {
        return back()->with('error', 'Invalid payment method. Please try again.');
    }
}





  public function deletePaymentMethod(Request $request, $paymentMethodId)
    {

      $user = auth()->user();
        try {
            $user->deletePaymentMethod($paymentMethodId);
            return back()->with('success', 'Payment method deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to delete payment method. Please ensure it exists and is not the current default if other cards are available.']);
        }
    }


















     public function legal_documentation(Request $request)
    {
        $plans =Plan::orderBy('position')->where('is_public', true)->get();
        $faqs = Faq::orderBy('position')->get();
       return view('subscriber.legaldocumentation ', compact('plans','faqs'));
    }

     public function account_dashboard(Request $request)
    {
        $plans =Plan::orderBy('position')->where('is_public', true)->get();
       return view('subscriber.account ', compact('plans'));
    }


// public function profile_update(Request $request)
// {
//     $user = auth()->user();
//     $formName = $request->formname;
    
//     try {
//         switch ($formName) {
//             case 'nameForm':
//                 $validated = $request->validate([
//                     'name' => 'required|string|max:255'
//                 ]);
//                 $user->name = $validated['name'];
//                 break;
                
//             case 'phoneForm':
//                 $validated = $request->validate([
//                     'mobile' => [
//                         'required',
//                         'string',
//                         'min:10',
//                         'max:15',
//                         'regex:/^\+?\d{10,15}$/'
//                     ],
//                 ], [
//                     'mobile.required' => 'Please enter your mobile number.',
//                     'mobile.regex' => 'Please enter a valid 10-digit mobile number (e.g., 9876543210).',
//                     'mobile.min' => 'Mobile number must be at least 10 digits.',
//                     'mobile.max' => 'Your mobile number cannot be longer than 15 characters.',
//                 ]);
//                 $user->mobile = $validated['mobile'];
//                 break;
                
//             case 'passwordForm':
//                 $validated = $request->validate([
//                     'current_password' => 'required|current_password',
//                     'password' => 'required|min:8|confirmed'
//                 ]);
//                 $user->password = Hash::make($validated['password']);
//                 break;
                
//             default:
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Invalid form submission'
//                 ], 400);
//         }
        
//         $user->save();
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Profile updated successfully',
//             'new_name' => $formName === 'nameForm' ? $user->name : null,
//             'new_mobile' => $formName === 'phoneForm' ? $user->mobile : null
//         ]);
        
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation failed',
//             'errors' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }



    public function profile_update(Request $request)
{
    $user = auth()->user();
    $formName = $request->formname;
    
    try {
        switch ($formName) {
            case 'nameForm':
                $validated = $request->validate([
                    'name' => 'required|string|max:255'
                ]);
                $user->name = $validated['name'];
                break;
                
            case 'phoneForm':
                $validated = $request->validate([
                    'mobile' => [
                        'required',
                        'string',
                        'min:10',
                        'max:15',
                        'regex:/^\+?\d{10,15}$/',
                        'unique:users,mobile,' . $user->id, 
                    ],
                ], [
                    'mobile.required' => 'Please enter your mobile number.',
                    'mobile.regex' => 'Please enter a valid 10-15 digit mobile number (e.g., +919876543210).',
                    'mobile.min' => 'Mobile number must be at least 10 digits.',
                    'mobile.max' => 'Your mobile number cannot be longer than 15 characters.',
                    'mobile.unique' => 'This mobile number is already in use.',
                ]);
                $user->mobile = $validated['mobile'];
                break;
                
            case 'passwordForm':
                $validated = $request->validate([
                    'current_password' => [
                        'required',
                        'string',
                        function ($attribute, $value, $fail) use ($user) {
                            if (!Hash::check($value, $user->password)) {
                                $fail('The current password is incorrect.');
                            }
                        },
                    ],
                    'password' => [
                        'required',
                        'string',
                        'confirmed',
                        Password::min(8)
                            ->max(32)
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised(), // Checks against password leaks
                    ],
                ], [
                    'current_password.required' => 'Current password is required',
                    'password.required' => 'New password is required',
                    'password.confirmed' => 'Passwords do not match',
                    'password.min' => 'Password must be at least 8 characters',
                    'password.max' => 'Password cannot exceed 32 characters',
                    'password.mixed_case' => 'Password must contain both uppercase and lowercase letters',
                    'password.numbers' => 'Password must contain at least one number',
                    'password.symbols' => 'Password must contain at least one special character',
                    'password.uncompromised' => 'This password has appeared in a data leak. Please choose a different password.',
                ]);
                
                $user->password = Hash::make($validated['password']);
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid form submission'
                ], 400);
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'new_name' => $formName === 'nameForm' ? $user->name : null,
            'new_mobile' => $formName === 'phoneForm' ? $user->mobile : null
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}



        public function user_destroy()
        {
           $user = Auth::user();

           try {
            $rawPhone = $user->mobile;
            $defaultRegion = env('DEFAULT_REGION');
            $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);

            if ($phoneInfo['valid']) {
                $phoneNumber = $phoneInfo['formatted'];
                $country = $phoneInfo['country'];
                $link =  route('register'); 

                $message = "Hi {$user->name}! 👋\n\n" .
                "Your account is being deleted.\n" .
                "If this was a mistake or you wish to rejoin, sign up again here 👉 {$link}";

                $response = \App\Services\SmsService::sendSms($phoneNumber, $message);

                echo $response['status'] === 'success'
                ? "✅ SMS sent to {$phoneNumber} ({$country})<br>"
                : "⚠️ SMS failed for {$phoneNumber}: " . ($response['error'] ?? 'Unknown error') . "<br>";
            } else {
                echo "❌ Invalid phone number for user {$user->email}: {$rawPhone}<br>";
            }

        } catch (Exception $e) {
            echo "💥 Exception for {$user->email}: " . $e->getMessage() . "<br>";
        }


           $user->notify(new AccountDeleted($user));
           $adminNotifiable = new AdminNotifiable();
           $adminNotifiable->email = env('ADMIN_CONTACT_EMAIL');
           $adminNotifiable->notify(new UserAccountDeletedNotification($user));

           // $adminNotifiable = new AdminNotifiable();
           // $adminNotifiable->email = env('ADMIN_CONTACT_Test');
           // $adminNotifiable->notify(new UserAccountDeletedNotification($user));

           Auth::logout(); 
           $user->delete(); 
           return redirect()->route('login')->with('status', 'Your account has been permanently deleted.');
        }


        public function entries()
        {
            $user = Auth::user();
            if ($user) {
                $userEntries = PrizeDrawEntry::where('user_id', $user->id)
                                    ->with('packageSubscription.plan') // Eager-load the relationships
                                    ->latest()
                                    ->get();
                return view('subscriber.entries', compact('userEntries')); 
            }
            return redirect()->route('login');
        }

        public function packagesubscriptionentries($package_subscriptions_id)
        {
            $user = Auth::user();
            if ($user) {
                $userEntries = PrizeDrawEntry::where('user_id', $user->id)->where('package_subscriptions_id', $package_subscriptions_id)
                                    ->with('packageSubscription.plan') // Eager-load the relationships
                                    ->latest()
                                    ->get();
                return view('subscriber.entries', compact('userEntries')); 
            }
            return redirect()->route('login');
        }

        public function read($id)
        {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return back()->with('status', 'Notification marked as read.');
        }


        public function invoices()
        {
            $user = Auth::user();
            
        // Get all invoices for the authenticated user
            $allInvoices = $user->invoices();

        // Filter for one-time payments (invoices without a subscription ID)
            $oneTimeInvoices = $allInvoices->filter(function ($invoice) {
                return is_null($invoice->subscription);
            });

        // Filter for recurring subscriptions (invoices with a subscription ID)
            $subscriptionInvoices = $allInvoices->filter(function ($invoice) {
                return !is_null($invoice->subscription);
            });

            return view('subscriber.invoices', compact('oneTimeInvoices', 'subscriptionInvoices'));
        }



        public function markAllAsRead()
        {
            Auth::user()->unreadNotifications->markAsRead();
            return back();
        }






        public function api_old(Request $request)
        {
            $start_date = $request->input('start');
            $end_date   = $request->input('end');
            $today = Carbon::today('Australia/Sydney');
            $query = Events::query();
            $query->where(function ($q) use ($today) {
                $q->where(function ($q1) use ($today) {
                    $q1->whereNotNull('end')
                    ->whereDate('end', '>=', $today);
                })->orWhere(function ($q2) use ($today) {
                    $q2->whereNull('end')
                    ->whereDate('start', '>=', $today);
                });
            });
            if ($start_date && $end_date) {
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->whereNotNull('end')
                    ->where('end', '>=', Carbon::parse($start_date))
                    ->where('start', '<=', Carbon::parse($end_date));
                })->orWhere(function ($q) use ($start_date, $end_date) {
                    $q->whereNull('end')
                    ->whereBetween('start', [
                      Carbon::parse($start_date),
                      Carbon::parse($end_date)
                  ]);
                });
            }

            $events = $query->orderBy('start', 'asc')->get()->map(function ($event) {
                $start = Carbon::parse($event->start, 'Australia/Sydney');
                $end   = $event->end
                ? Carbon::parse($event->end, 'Australia/Sydney')->addDay()
                : null;

                return [
                    'id'    => $event->id,
                    'title' => $event->name,
                    'start' => $start->toIso8601String(),
                    'end'   => $end ? $end->toIso8601String() : null,

                    'custom_start' => $start->format('g:i A jS \of F Y'),
                    'custom_end'   => $event->end
                    ? Carbon::parse($event->end, 'Australia/Sydney')
                    ->format('g:i A jS \of F Y')
                    : null,

                    'allDay' => true,
                    'url'    => $event->facebook_url,
                    'image'  => $event->image
                    ? asset('storage/' . $event->image)
                    : asset('images/australian-dollar.png'),
                    'color'  => '#fad604',
                ];
            });

            return response()->json($events);
        }



        public function api(Request $request)
{
    $start_date = $request->input('start');
    $end_date   = $request->input('end');
    $today = Carbon::today('Australia/Sydney');

    // ── Events query ──────────────────────────────────────────────
    $query = Events::query();
    $query->where(function ($q) use ($today) {
        $q->where(function ($q1) use ($today) {
            $q1->whereNotNull('end')
               ->whereDate('end', '>=', $today);
        })->orWhere(function ($q2) use ($today) {
            $q2->whereNull('end')
               ->whereDate('start', '>=', $today);
        });
    });

    if ($start_date && $end_date) {
        $query->where(function ($q) use ($start_date, $end_date) {
            $q->whereNotNull('end')
              ->where('end', '>=', Carbon::parse($start_date))
              ->where('start', '<=', Carbon::parse($end_date));
        })->orWhere(function ($q) use ($start_date, $end_date) {
            $q->whereNull('end')
              ->whereBetween('start', [
                  Carbon::parse($start_date),
                  Carbon::parse($end_date),
              ]);
        });
    }

    $events = $query->orderBy('start', 'asc')->get()->map(function ($event) {
        $start = Carbon::parse($event->start, 'Australia/Sydney');
        $end   = $event->end
            ? Carbon::parse($event->end, 'Australia/Sydney')->addDay()
            : null;

        return [
            'id'           => $event->id,
            'title'        => $event->name,
            'start'        => $start->toIso8601String(),
            'end'          => $end ? $end->toIso8601String() : null,
            'custom_start' => $start->format('g:i A jS \of F Y'),
            'custom_end'   => $event->end
                ? Carbon::parse($event->end, 'Australia/Sydney')->format('g:i A jS \of F Y')
                : null,
            'allDay'       => true,
            'url'          => $event->facebook_url,
            'image'        => $event->image
                ? asset('storage/' . $event->image)
                : asset('images/australian-dollar.png'),
            'color'        => '#fad604',
            'type'         => 'event',
        ];
    });

    // ── Prize Draws query ─────────────────────────────────────────
    $drawQuery = PrizeDraw::whereNull('deleted_at')
        ->where('show_on_site', 1)
        ->whereDate('draw_date', '>=', $today);

    if ($start_date && $end_date) {
        $drawQuery->whereBetween('draw_date', [
            Carbon::parse($start_date),
            Carbon::parse($end_date),
        ]);
    }

    $draws = $drawQuery->orderBy('draw_date', 'asc')->get()->map(function ($draw) {
        $drawDate = Carbon::parse($draw->draw_date, 'Australia/Sydney');

        return [
            'id'           => $draw->id,
            'title'        => $draw->title,
            'start'        => $drawDate->toIso8601String(),
            'end'          => $drawDate->copy()->addDay()->toIso8601String(),
            'custom_start' => $drawDate->format('g:i A jS \of F Y'),
            'custom_end'   => null,
            'allDay'       => true,
            'url'          => null,
            'image'        => asset('storage/' . $draw->prize_image),
            'color'        => '#e74c3c',   // distinct colour for draws
            'type'         => 'prize_draw',
            // extra draw-specific fields your front-end may want
            'prize_type'   => $draw->prize_type,
            'cash_amount'  => $draw->cash_amount,
            'prize_title'  => $draw->prize_title,
            'prize_value'  => $draw->prize_value_amount,
        ];
    });

    // ── Merge & sort by start ─────────────────────────────────────
    $merged = $events->concat($draws)
        ->sortBy('start')
        ->values();

    return response()->json($merged);
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
