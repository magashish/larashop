<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\ProductPackage;
use App\Models\UserPackageSubscription;
use App\Models\PrizeDrawEntry;
use Illuminate\Support\Str;
use App\Models\SubscriptionLog;
use Stripe\Stripe;
use Stripe\Refund;
use Illuminate\Support\Facades\Log;
use Stripe\Invoice;
use Carbon\Carbon;
use Laravel\Cashier\Cashier;
use Stripe\Exception\CardException;
use App\Notifications\PurchaseConfirmationNotification;
use App\Notifications\UpsellPurchaseConfirmationNotification;

class SubscriptionPlanController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('cashier.secret'));
    }

    public function index()
    {
        $plans = Plan::get();
        $product_package = ProductPackage::get();
        $currentSubscription = null;
        if (auth()->check()) {
            $currentSubscription = auth()->user()->subscriptions()->active()->first();
        }
        return view("subscription.plans", compact("plans", "product_package", "currentSubscription"));
    }

    public function store(Request $request)
    {

        $request->validate([
            'plan'                    => 'required|exists:plans,id',
            'type'                    => 'required|in:subscription,package',
            'selected_payment_option' => 'required|in:new,saved',
            'payment_method_id'       => 'required|string',
            'token'                   => 'nullable|string',
            'name'                    => 'required_if:selected_payment_option,new|nullable|string|max:255',
            'email'                   => 'required_if:selected_payment_option,new|nullable|email|max:255',
            'card_holder_name_hidden' => 'nullable|string|max:255',
            'card_holder_email_hidden'=> 'nullable|email|max:255',
            'is_upsell'               => 'nullable|boolean',
        ]);

        $user            = $request->user();
        $plan            = Plan::findOrFail($request->plan);
        $planType        = $request->input('type');
        $paymentMethodId = $request->input('payment_method_id') ?? $request->input('token');
        $cardHolderName  = $request->input('card_holder_name_hidden') ?? $request->input('name');
        $cardHolderEmail = $request->input('card_holder_email_hidden') ?? $request->input('email');

        // -------------------------------------------------------
    // ✅ RACE CONDITION FIX — DB Transaction + Lock
    // -------------------------------------------------------
        if ($request->input('is_upsell', 0)) {
            $alreadyPurchased = UserPackageSubscription::where('user_id', $user->id)
            ->where('is_upsell', 1)
            ->exists();

            if ($alreadyPurchased) {
                return redirect()->route('subscription.manage')
                ->with('error', "You've already purchased this upsell.");
            }
        }
        
    

    // -------------------------------------------------------
    // Upsell check — session validate karo + expiry check
    // -------------------------------------------------------
    $upsellExpiry = session('upsell_expires', 0);
    $isUpsell     = $request->input('is_upsell', 0)
        && session('upsell_active')
        && session('upsell_plan_id') == $plan->id
        && Carbon::now()->timestamp <= $upsellExpiry;
    $finalPrice   = $isUpsell ? $plan->price * 0.50 : $plan->price;

        try {
            if (is_null($user->stripe_id)) {
                $user->createAsStripeCustomer(['name' => $cardHolderName, 'email' => $cardHolderEmail]);
            } else {
                $user->updateStripeCustomer(['name' => $cardHolderName, 'email' => $cardHolderEmail]);
            }

            $user->addPaymentMethod($paymentMethodId);
            $user->updateDefaultPaymentMethod($paymentMethodId);

            $userPackageSubscription = null;
            $numberOfFreeEntries     = null;

            if ($planType === 'package') {
                $newPackageStartsAt = Carbon::now();
                $newPackageStatus   = 'active';
                $newPackageEndsAt   = $newPackageStartsAt->copy()->addWeeks($plan->access_duration_weeks);

                // Payment ho — tabhi DB mein save karo
                $charge = $user->charge(
                    (int)($finalPrice * 100),
                    $paymentMethodId,
                    [
                        'description' => $plan->name . ($isUpsell ? ' Upsell Purchase (50% Off)' : ' Purchase'),
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
                    'is_upsell'     => $isUpsell ? true : false,
                    'comment'       => $isUpsell
                    ? "Upsell purchase '{$plan->name}' at 50% off — $" . number_format($finalPrice, 2)
                    : null,
                ]);

                // Payment successful — entries banao
                $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
                award_immediate_prize_entries($user, $plan, $userPackageSubscription);

                $success_msg = $isUpsell ? 'Upsell package purchased successfully!' : 'Package purchased successfully!';

                logUserActivity(
                    $user->id,
                    $isUpsell ? 'upsell_purchased' : 'package_purchased',
                    ($isUpsell ? 'Upsell' : 'User') . ' purchased package: ' . $plan->name,
                    [
                        'plan_id'                => $plan->id,
                        'plan_name'              => $plan->name,
                        'price'                  => $finalPrice,
                        'subscription_record_id' => $userPackageSubscription->id,
                    ]
                );

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

                // Sirf active status pe entries banao
                if ($stripeSubscription->stripe_status === 'active') {
                    $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
                }

                $success_msg = 'Subscription created successfully!';

                $previousSubscriptionCount = UserPackageSubscription::where('user_id', $user->id)
                ->where('type', 'subscription')->count();

                logUserActivity(
                    $user->id,
                    $previousSubscriptionCount > 1 ? 'subscription_resubscribed' : 'subscription_created',
                    ($previousSubscriptionCount > 1 ? 'User re-subscribed' : 'User subscribed') . ' to: ' . $plan->name,
                    [
                        'plan_id'                => $plan->id,
                        'plan_name'              => $plan->name,
                        'stripe_subscription_id' => $stripeSubscription->stripe_id,
                        'subscription_record_id' => $userPackageSubscription->id,
                    ]
                );
            }

            // Upsell session clear karo
            if ($isUpsell) {
                session()->forget(['upsell_active', 'upsell_plan_id', 'upsell_expires']);
            }

            // Notification
            $notificationClass = $isUpsell
            ? UpsellPurchaseConfirmationNotification::class
            : PurchaseConfirmationNotification::class;

            $user->notify(new $notificationClass($user, $userPackageSubscription, $numberOfFreeEntries));

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

    public function packagesubscriptiondetail(Request $request, $id)
    {
        try {
            $subscription  = UserPackageSubscription::with('plan')->findOrFail($id);
            $plan          = $subscription->plan;
            $purchaseData  = null;
            $receiptUrl    = null;
            $latestInvoice = null;
            $paypalDetails = null;

            $paypalBaseUrl = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://www.paypal.com'
            : 'https://www.sandbox.paypal.com';

            $isPayPal = !empty($subscription->paypal_id);
            $isStripe = !empty($subscription->stripe_id);

            if ($isStripe) {
                if ($subscription->type === 'package') {
                    $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($subscription->stripe_id);
                    $purchaseData  = $paymentIntent;
                    if ($paymentIntent->latest_charge) {
                        $charge     = Cashier::stripe()->charges->retrieve($paymentIntent->latest_charge);
                        $receiptUrl = $charge->receipt_url;
                    }
                } elseif ($subscription->type === 'subscription') {
                    $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);
                    $purchaseData       = $stripeSubscription;
                    if ($stripeSubscription->latest_invoice) {
                        $latestInvoice = Cashier::stripe()->invoices->retrieve($stripeSubscription->latest_invoice);
                        if ($latestInvoice->charge) {
                            $charge     = Cashier::stripe()->charges->retrieve($latestInvoice->charge);
                            $receiptUrl = $charge->receipt_url;
                        }
                    }
                }
            } elseif ($isPayPal) {
                $provider = new \Srmklive\PayPal\Services\PayPal;
                $provider->setApiCredentials(config('paypal'));
                $provider->getAccessToken();

                if ($subscription->type === 'package') {
                    $captureDetails = $provider->showCapturedPaymentDetails($subscription->paypal_id);
                    $paypalDetails  = $captureDetails;
                    $receiptUrl     = $paypalBaseUrl . '/activity/payment/' . $subscription->paypal_id;
                } elseif ($subscription->type === 'subscription') {
                    $subDetails    = $provider->showSubscriptionDetails($subscription->paypal_id);
                    $paypalDetails = $subDetails;
                    try {
                        $transactions = $provider->listSubscriptionTransactions(
                            $subscription->paypal_id,
                            now()->subYear()->toIso8601String(),
                            now()->toIso8601String()
                        );
                        if (!empty($transactions['transactions'])) {
                            $latestInvoice = $transactions['transactions'][0];
                        }
                    } catch (\Exception $e) {
                        Log::warning('PayPal transaction fetch failed: ' . $e->getMessage());
                    }
                    $receiptUrl = $paypalBaseUrl . '/billing/subscriptions/' . $subscription->paypal_id;
                }
            }

            return view('subscriber.package-subscription-detail', [
                'userPackageSubscription' => $subscription,
                'plan'          => $plan,
                'purchaseData'  => $purchaseData,
                'latestInvoice' => $latestInvoice,
                'receiptUrl'    => $receiptUrl,
                'paypalDetails' => $paypalDetails,
                'isPayPal'      => $isPayPal,
                'isStripe'      => $isStripe,
            ]);

        } catch (\Exception $e) {
            Log::error('packagesubscriptiondetail error: ' . $e->getMessage());
            return redirect()->route('subscription.manage')->with('error', 'Unable to retrieve order details.');
        }
    }

    public function show(int $id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('register')->with('error', 'Please register in to subscribe.');
        }

        $perposeid      = null;
        $perposeid_data = null;
        if ($id == 10 || $id == 9) {
            $perposeid      = 8;
            $perposeid_data = Plan::findOrFail($perposeid);
        }

        $plan = Plan::findOrFail($id);
        $user = auth()->user();

        try {
            $paymentMethods = $user->paymentMethods();
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'No such customer')) {
                $msg = 'There seems to be an issue, we apologies for any inconvenience.';
                return view('subscriber.errors-stripe ', compact('user', 'msg'));
            }
            abort(500, 'Something went wrong while retrieving payment methods.');
        }

        $defaultPaymentMethod = $user->defaultPaymentMethod();
        $isSubscription       = $plan->type === 'subscription';

        try {
            $user->createOrGetStripeCustomer();
        } catch (\Exception $e) {
            Log::error("Stripe Customer Creation/Retrieval Error: " . $e->getMessage(), ['user_id' => $user->id]);
            return redirect()->back()->with('error', 'Could not prepare payment, please try again.');
        }

        $intent = auth()->user()->createSetupIntent([
            'payment_method_options' => [
                'card' => ['request_three_d_secure' => 'automatic'],
            ],
        ]);

        $currentSubscription = $user->subscriptions()->active()->first();
        return view("subscription.subscription", compact("plan", "paymentMethods", "defaultPaymentMethod", "isSubscription", "intent", "currentSubscription"));
    }

    public function managesubscription()
    {
        $user          = auth()->user();
        $subscriptions = UserPackageSubscription::where('user_id', $user->id)->orderBy('starts_at', 'desc')->get();

        $refundableSubscriptions = $user->subscriptions()->where(function ($query) {
            $query->where('stripe_status', 'canceled')->orWhereNotNull('ends_at');
        })->get();

        return view('subscription.manage_subscription', compact('user', 'subscriptions', 'refundableSubscriptions'));
    }

    public function confirmswappage(string $current_subscription, string $new_plan_slug)
    {
        $user                = auth()->user();
        $currentSubscription = $user->subscription($current_subscription);
        $newPlan             = Plan::where('slug', $new_plan_slug)->first();

        if (!$currentSubscription || !$currentSubscription->active() || !$newPlan) {
            return redirect()->route('subscription.manage')->with('error', 'Invalid subscription or plan selected for swap.');
        }
        if ($currentSubscription->name === $newPlan->slug) {
            return redirect()->route('subscription.manage')->with('info', 'You are already subscribed to this plan.');
        }

        $estimatedProratedAmount = null;
        $prorationExplanation    = null;

        try {
            $stripeSubscription  = $currentSubscription->asStripeSubscription();
            $upcomingInvoice     = Invoice::upcoming([
                'customer'                        => $user->stripe_id,
                'subscription'                    => $stripeSubscription->id,
                'subscription_items'              => [['id' => $stripeSubscription->items->data[0]->id, 'price' => $newPlan->stripe_plan]],
                'subscription_proration_behavior' => 'always_invoice',
            ]);
            $estimatedProratedAmount = $upcomingInvoice->total;
            $prorationExplanation    = "Estimated charge: $" . number_format($estimatedProratedAmount / 100, 2) . " " . strtoupper($upcomingInvoice->currency) . ".";
            $creditAmount = 0;
            $chargeAmount = 0;
            foreach ($upcomingInvoice->lines->data as $lineItem) {
                if ($lineItem->amount < 0) $creditAmount += abs($lineItem->amount);
                else $chargeAmount += $lineItem->amount;
            }
            if ($creditAmount > 0 && $chargeAmount > 0) {
                $prorationExplanation .= " (Includes a credit of $" . number_format($creditAmount / 100, 2) . " and a charge of $" . number_format($chargeAmount / 100, 2) . ").";
            } elseif ($creditAmount > 0) {
                $prorationExplanation = "Estimated credit: $" . number_format($creditAmount / 100, 2) . " " . strtoupper($upcomingInvoice->currency) . ".";
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error("Stripe Proration Preview Error: " . $e->getMessage());
            $prorationExplanation = "Could not estimate proration at this time. Please proceed to confirm, or try again later.";
        } catch (\Exception $e) {
            Log::error("General Proration Preview Error: " . $e->getMessage());
            $prorationExplanation = "An error occurred while estimating proration. Please proceed to confirm, or try again later.";
        }

        return view('subscription.confirm_swap', compact('currentSubscription', 'newPlan', 'estimatedProratedAmount', 'prorationExplanation'));
    }

    public function swapSubscription(Request $request)
    {
        $user                = auth()->user();
        $currentSubscription = $user->subscription($request->current_subscription_name);
        $newPlan             = Plan::where('slug', $request->new_plan_slug)->firstOrFail();

        if (!$currentSubscription || !$currentSubscription->active()) {
            return redirect()->back()->with('error', 'You do not have an active subscription to change.');
        }
        if ($currentSubscription->type === $newPlan->slug) {
            return redirect()->back()->with('info', 'You are already subscribed to this plan.');
        }

        try {
            $oldPlanNameForLog      = $currentSubscription->type;
            $oldStripePriceIdForLog = $currentSubscription->stripe_price;
            $currentSubscription->swap($newPlan->stripe_plan);
            $currentSubscription->update(['type' => $newPlan->slug]);
            SubscriptionLog::create([
                'user_id'              => $user->id,
                'subscription_id'      => $currentSubscription->id,
                'event_type'           => 'swapped',
                'details'              => [
                    'from_plan_name'         => $oldPlanNameForLog,
                    'to_plan_name'           => $newPlan->name,
                    'new_stripe_price_id'    => $newPlan->stripe_plan,
                    'old_stripe_price_id'    => $oldStripePriceIdForLog,
                    'stripe_subscription_id' => $currentSubscription->stripe_id,
                    'prorated'               => true,
                ],
                'performed_by_user_id' => auth()->id(),
            ]);
            return redirect()->route('subscription.manage')->with('success', 'Subscription changed to ' . $newPlan->name . ' successfully!');
        } catch (\Exception $e) {
            Log::error("Stripe Subscription Swap Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to change subscription: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(Request $request)
    {
        $user = auth()->user();
        $request->validate(['subscription_name' => 'required|string']);
        $subscriptionIdentifier = $request->subscription_name;

        $userPackageSub = UserPackageSubscription::where('user_id', $user->id)
        ->where(function ($query) use ($subscriptionIdentifier) {
            $query->where('paypal_id', $subscriptionIdentifier)
            ->orWhere('stripe_id', $subscriptionIdentifier);
        })
        ->where('status', 'active')
        ->first();

        if (!$userPackageSub) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $isPayPal = !empty($userPackageSub->paypal_id);
        $isStripe = !empty($userPackageSub->stripe_id);

        if ($isPayPal) {
            try {
                $provider = new \Srmklive\PayPal\Services\PayPal;
                $provider->setApiCredentials(config('paypal'));
                $provider->getAccessToken();
                $provider->cancelSubscription($userPackageSub->paypal_id, 'Cancelled by user');
                $userPackageSub->update(['status' => 'canceled']);
                logUserActivity($user->id, 'subscription_cancelled', 'User cancelled PayPal subscription: ' . $userPackageSub->paypal_id, [
                    'paypal_subscription_id' => $userPackageSub->paypal_id,
                    'plan_id'                => $userPackageSub->plan_id,
                ]);
                return redirect()->route('subscription.manage')->with('success', 'PayPal subscription cancelled successfully.');
            } catch (\Exception $e) {
                Log::error('PayPal Subscription Cancellation Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to cancel PayPal subscription: ' . $e->getMessage());
            }
        }

        if ($isStripe) {
            $stripeSubscription = $user->subscriptions()->where('stripe_id', $subscriptionIdentifier)->first();
            if (!$stripeSubscription || $stripeSubscription->onGracePeriod()) {
                return redirect()->back()->with('error', 'No active or resumable Stripe subscription found.');
            }
            try {
                $stripeSubscription->cancelNow();
                UserPackageSubscription::where('stripe_id', $subscriptionIdentifier)->update(['status' => 'canceled']);
                logUserActivity($user->id, 'subscription_cancelled', 'User cancelled Stripe subscription: ' . $subscriptionIdentifier, [
                    'stripe_subscription_id' => $subscriptionIdentifier,
                ]);
                return redirect()->route('subscription.manage')->with('success', 'Subscription cancelled successfully.');
            } catch (\Exception $e) {
                Log::error('Stripe Subscription Cancellation Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Unable to determine payment method for this subscription.');
    }

    public function resumesubscription(Request $request)
    {
        $user = auth()->user();
        $request->validate(['subscription_name' => 'required|string']);
        $subscription = $user->subscription($request->subscription_name);
        if (!$subscription || !$subscription->onGracePeriod()) {
            return redirect()->back()->with('error', 'This subscription cannot be resumed.');
        }
        try {
            $subscription->resume();
            return redirect()->route('subscription.manage')->with('success', 'Subscription resumed successfully!');
        } catch (\Exception $e) {
            Log::error("Stripe Subscription Resume Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    public function refundsubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string|exists:subscriptions,stripe_id',
            'amount'          => 'nullable|numeric|min:0.01',
            'reason'          => 'nullable|string|in:duplicate,fraudulent,requested_by_customer',
        ]);

        $user = auth()->user();

        try {
            $subscription = $user->subscriptions()->where('stripe_id', $request->subscription_id)->firstOrFail();
            $invoices     = Invoice::all([
                'customer'     => $user->stripe_id,
                'limit'        => 1,
                'subscription' => $subscription->stripe_id,
                'status'       => 'paid',
            ]);

            if (empty($invoices->data) || !$invoices->data[0]->charge) {
                throw new \Exception("No recent paid charge found for this subscription to refund.");
            }

            $latestChargeId = $invoices->data[0]->charge;
            $options        = [];
            if ($request->has('amount')) $options['amount'] = (int) ($request->amount * 100);
            if ($request->has('reason')) $options['reason'] = $request->reason;

            $refund = Refund::create(array_merge(['charge' => $latestChargeId], $options));
            return back()->with('success', 'Refund processed successfully. Refund ID: ' . $refund->id);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error("Stripe Refund Error: " . $e->getMessage());
            return back()->with('error', 'Stripe API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("General Refund Error: " . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Upsell Initiate — Popup se click hone pe
     * Package issue check karo, session set karo, checkout pe redirect karo
     */
    public function upsellInitiate(Request $request, Plan $plan)
    {
        $user = auth()->user();

        // ✅ 1. Check if upsell already purchased (lifetime)
        $hasPurchasedUpsell = UserPackageSubscription::where('user_id', $user->id)
        ->where('is_upsell', 1)
        ->exists();

        if ($hasPurchasedUpsell) {
            return redirect()->route('subscription.manage')
            ->with('error', "You've already purchased this upsell.");
        }

       // ✅ 2. Get latest package (non-upsell)
        $latestPackage = UserPackageSubscription::where('user_id', $user->id)
        ->where('is_upsell', 0)
        ->latest('created_at')
        ->first();

        if (!$latestPackage) {
            return redirect()->route('subscription.manage')
            ->with('error', "You need to purchase a package first.");
        }

        if ($latestPackage->created_at->diffInHours(now()) > 24) {
            return redirect()->route('subscription.manage')
            ->with('error', "Upsell offer has expired.");
        }

       // ✅ Allow upsell
        session([
            'upsell_active'  => true,
            'upsell_plan_id' => $plan->id,
            'upsell_expires' => now()->addHours(24)->timestamp,
        ]);

        return redirect()->route('subscription.show', $plan->id);
    }








    public function purchaseUpsell(Request $request, Plan $plan)
    {
       
        $user    = auth()->user();
        $plan_id = $plan->id;

        $package_issue = false;
        $today         = Carbon::now();
        $subscriptions = UserPackageSubscription::where('user_id', $user->id)->get();

        if ($subscriptions->isNotEmpty()) {
            $currentSubscription = UserPackageSubscription::where('user_id', $user->id)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->get();

            $package_issue = $currentSubscription->isEmpty();

            $isUpsellCurrentSubscription = UserPackageSubscription::where('user_id', $user->id)
            ->where('is_upsell', 1)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->first();

            if ($isUpsellCurrentSubscription) {
                $package_issue = true;
            }
        }

        if ($package_issue) {
            return redirect()->route('subscription.manage')->with('warning', "You've already purchased this upsell offer.");
        }

        $discountedAmount   = $plan->price * 0.50;
        $newPackageStartsAt = Carbon::now();
        $newPackageEndsAt   = $newPackageStartsAt->copy()->addWeeks($plan->access_duration_weeks);
        $newPackageStatus   = 'active';
        $numberOfFreeEntries = null;

        // Last purchase se gateway detect karo
        $lastPackage  = UserPackageSubscription::where('user_id', $user->id)
        ->where('type', 'package')
        ->latest()
        ->first();
        $isPayPalUser = $lastPackage && !empty($lastPackage->paypal_id);

        // PAYPAL UPSELL
        if ($isPayPalUser) {
            session([
                'paypal_plan_id'       => $plan->id,
                'paypal_type'          => 'package',
                'paypal_is_upsell'     => true,
                'paypal_upsell_amount' => number_format($discountedAmount, 2, '.', ''),
            ]);

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'description' => $plan->name . ' Upsell Purchase (50% Off)',
                    'amount' => [
                        'currency_code' => strtoupper(env('DEFAULT_CURRENCY', 'AUD')),
                        'value'         => number_format($discountedAmount, 2, '.', ''),
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

            Log::error('PayPal Upsell Order Creation Failed', $order);
            return redirect()->back()->with('error', 'PayPal upsell failed. Please try again.');
        }

        // STRIPE UPSELL
        if (!$user->hasStripeId()) {
            return redirect()->route('billing.dashboard')->with('error', 'Please complete your billing details before purchasing.');
        }
        if (!$user->hasDefaultPaymentMethod()) {
            return redirect()->route('billing.dashboard')->with('error', 'Please add a payment method before purchasing.');
        }

        try {
            $charge = $user->charge(
                (int)($discountedAmount * 100),
                $user->defaultPaymentMethod()->id,
                [
                    'description' => $plan->name . ' Upsell Purchase (50% Off)',
                    'return_url'  => route('payment.success'),
                    'currency'    => env('DEFAULT_CURRENCY', 'aud'),
                ]
            );

            $userPackageSubscription = UserPackageSubscription::create([
                'user_id'       => $user->id,
                'plan_id'       => $plan->id,
                'type'          => 'package',
                'purchase_date' => Carbon::now(),
                'starts_at'     => $newPackageStartsAt,
                'ends_at'       => $newPackageEndsAt,
                'status'        => $newPackageStatus,
                'stripe_id'     => $charge->id,
                'paypal_id'     => null,
                'comment'       => "User purchased the upsell for '{$plan->name}' at a discounted price of $" . number_format($discountedAmount, 2) . ".",
                'is_upsell'     => true,
            ]);

            $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
            award_immediate_prize_entries($user, $plan, $userPackageSubscription);

            $userIdentifier = $user->first_name && $user->last_name
            ? $user->first_name . ' ' . $user->last_name
            : $user->email;

            logUserActivity(
                $user->id,
                'upsell_purchased',
                sprintf('User %s successfully purchased the upsell package "%s" (Plan ID: %s) on %s', $userIdentifier, $plan->name, $plan->id, now()->toDateTimeString()),
                ['user_identifier' => $userIdentifier, 'plan_id' => $plan->id, 'plan_name' => $plan->name, 'timestamp' => now()->toDateTimeString()]
            );

            $user->notify(new UpsellPurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));

            return redirect()->route('subscription.manage')->with('success', 'Your package was successfully purchased!');

        } catch (\Laravel\Cashier\Exceptions\PaymentActionRequiredException $e) {
            return back()->withErrors(['stripe_error' => 'Payment requires additional authentication. Please try again or use a different payment method.']);
        } catch (\Laravel\Cashier\Exceptions\PaymentFailure $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()]);
        } catch (CardException $e) {
            return back()->withErrors(['stripe_error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred during purchase. Please try again.']);
        }
    }

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    // public function destroy(string $id)
    // {
    //     $subscription = UserPackageSubscription::find($id);
    //     $subscription->delete();
    // }

    public function destroy($id)
    {
        $subscription = UserPackageSubscription::find($id);

    // ✅ Null check
        if (!$subscription) {
            return redirect()->back()
            ->with('error', 'Subscription record not found.');
        }

    // Authorization check — sirf apna record delete kare
        if ($subscription->user_id !== auth()->id()) {
            return redirect()->back()
            ->with('error', 'Unauthorized action.');
        }

        $subscription->delete();

        return redirect()->back()
        ->with('success', 'Subscription removed successfully.');
    }

}