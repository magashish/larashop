<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\UserPackageSubscription;
use App\Models\PrizeDrawEntry;
use App\Models\User;
use Carbon\Carbon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Notifications\PurchaseConfirmationNotification;
use App\Notifications\UpsellPurchaseConfirmationNotification;

class PayPalController extends Controller
{
    /**
     * Step 1: User ko PayPal page pe redirect karo
     */
    public function redirect(Request $request)
    {
        $request->validate([
            'plan'      => 'required|exists:plans,id',
            'type'      => 'required|in:package,subscription',
            'is_upsell' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($request->plan);
        $type = $request->type;

        // ✅ Race condition fix
        if ($request->input('is_upsell', 0)) {
            $alreadyPurchased = UserPackageSubscription::where('user_id', $user->id)
            ->where('is_upsell', 1)
            ->exists();

            if ($alreadyPurchased) {
                return redirect()->route('subscription.manage')
                ->with('error', "You've already purchased this upsell.");
            }
        }

        // Upsell check — session validate + expiry
        $upsellExpiry = session('upsell_expires', 0);
        $isUpsell     = $request->input('is_upsell', 0)
        && session('upsell_active')
        && session('upsell_plan_id') == $plan->id
        && Carbon::now()->timestamp <= $upsellExpiry;
        $finalPrice   = $isUpsell ? $plan->price * 0.50 : $plan->price;

        session([
            'paypal_plan_id'   => $plan->id,
            'paypal_type'      => $type,
            'paypal_is_upsell' => $isUpsell,
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $currency = strtoupper(env('DEFAULT_CURRENCY', 'AUD'));

        if ($type === 'package') {
            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'description' => $plan->name . ($isUpsell ? ' Upsell Package (50% Off)' : ' Package'),
                    'amount' => [
                        'currency_code' => $currency,
                        'value'         => number_format($finalPrice, 2, '.', ''),
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

            Log::error('PayPal Order Creation Failed', $order);
            return redirect()->back()->with('error', 'PayPal order creation failed. Please try again.');

        } else {
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
                    'email_address' => $user->email ?? '',
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

            Log::error('PayPal Subscription Creation Failed', $subscription);
            return redirect()->back()->with('error', 'PayPal subscription creation failed. Please try again.');
        }
    }

    /**
     * Step 2: PayPal approve ke baad user yahan aata hai
     */
    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $planId         = session('paypal_plan_id');
        $type           = session('paypal_type');
        $isUpsell       = session('paypal_is_upsell', false);
        $isRegistration = session()->has('paypal_reg_user_id');
        $plan           = Plan::findOrFail($planId);
        $user           = auth()->user();
        $finalPrice     = $isUpsell ? $plan->price * 0.50 : $plan->price;

        $thankYouRoutes = [
            11 => 'thankyousubscription',
            8  => 'thankyougold',
            20 => 'thankyousilver',
            21 => 'thankyoubronze',
        ];

        if ($type === 'package') {
            // -----------------------------------------------
            // ONE-TIME PACKAGE PAYMENT
            // -----------------------------------------------
            $orderId = session('paypal_order_id');

            if (!$orderId) {
                return redirect()->route('subscription.manage')
                ->with('error', 'Invalid PayPal session. Please try again.');
            }

            $result = $provider->capturePaymentOrder($orderId);

            if (isset($result['status']) && $result['status'] === 'COMPLETED') {

                $captureId          = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? $orderId;
                $newPackageStartsAt = Carbon::now();
                $newPackageEndsAt   = $newPackageStartsAt->copy()->addWeeks($plan->access_duration_weeks);

                $userPackageSubscription = UserPackageSubscription::create([
                    'user_id'       => $user->id,
                    'plan_id'       => $plan->id,
                    'type'          => 'package',
                    'purchase_date' => Carbon::now(),
                    'starts_at'     => $newPackageStartsAt,
                    'ends_at'       => $newPackageEndsAt,
                    'status'        => 'active',
                    'stripe_id'     => null,
                    'paypal_id'     => $captureId,
                    'is_upsell'     => $isUpsell ? true : false,
                    'comment'       => $isUpsell
                    ? "Upsell via PayPal '{$plan->name}' at 50% off — $" . number_format($finalPrice, 2)
                    : 'Paid via PayPal (one-time)',
                ]);

                Log::info('PayPal package COMPLETED — paypal_id: ' . $captureId . ' upsell: ' . ($isUpsell ? 'yes' : 'no') . ' reg: ' . ($isRegistration ? 'yes' : 'no'));

                // ✅ Package ke liye entries banao
                $numberOfFreeEntries = null;
                if (function_exists('award_first_purchase_bonus_entries')) {
                    $numberOfFreeEntries = award_first_purchase_bonus_entries($user, $userPackageSubscription);
                }
                if (function_exists('award_immediate_prize_entries')) {
                    award_immediate_prize_entries($user, $plan, $userPackageSubscription);
                }

                if (function_exists('logUserActivity')) {
                    logUserActivity(
                        $user->id,
                        $isUpsell ? 'upsell_purchased' : 'package_purchased',
                        ($isUpsell ? 'Upsell' : 'User') . ' purchased package via PayPal: ' . $plan->name,
                        [
                            'plan_id'           => $plan->id,
                            'plan_name'         => $plan->name,
                            'price'             => $finalPrice,
                            'paypal_capture_id' => $captureId,
                            'is_upsell'         => $isUpsell,
                            'is_registration'   => $isRegistration,
                        ]
                    );
                }

                if ($isUpsell && class_exists(UpsellPurchaseConfirmationNotification::class)) {
                    $user->notify(new UpsellPurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));
                } elseif (class_exists(PurchaseConfirmationNotification::class)) {
                    $user->notify(new PurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));
                }

                session()->forget([
                    'paypal_plan_id', 'paypal_type', 'paypal_order_id',
                    'paypal_is_upsell', 'upsell_active', 'upsell_plan_id',
                    'upsell_expires', 'paypal_reg_user_id',
                ]);

                $successMsg = $isUpsell
                ? 'Upsell package purchased successfully via PayPal!'
                : 'Package purchased successfully via PayPal!';

                if ($isRegistration) {
                    $route = $thankYouRoutes[$plan->id] ?? 'thank_you';
                    return redirect()->route($route)->with('success', $successMsg);
                }

                return redirect()->route('subscription.manage')->with('success', $successMsg);
            }

            Log::error('PayPal Capture Failed or not COMPLETED', $result);
            return redirect()->route('subscription.manage')
            ->with('error', 'PayPal payment capture failed. Please contact support.');

        } else {
            // -----------------------------------------------
            // SUBSCRIPTION
            // -----------------------------------------------
            $paypalSubId = $request->get('subscription_id') ?? session('paypal_subscription_id');

            if (!$paypalSubId) {
                return redirect()->route('subscription.manage')
                ->with('error', 'Invalid PayPal subscription session.');
            }

            $details = $provider->showSubscriptionDetails($paypalSubId);

            if (isset($details['status']) && $details['status'] === 'ACTIVE') {

                // ✅ PayPal ki actual next billing date lo
                $nextBillingDate = isset($details['billing_info']['next_billing_time'])
                ? Carbon::parse($details['billing_info']['next_billing_time'])
                    : Carbon::now()->addWeeks($plan->access_duration_weeks); // fallback

                    Log::info('PayPal next_billing_time: ' . ($details['billing_info']['next_billing_time'] ?? 'not found'));

                    $userPackageSubscription = UserPackageSubscription::create([
                        'user_id'       => $user->id,
                        'plan_id'       => $plan->id,
                        'type'          => 'subscription',
                        'purchase_date' => Carbon::now(),
                        'starts_at'     => Carbon::now(),
                    'ends_at'       => $nextBillingDate, // ✅ PayPal ki actual date
                    'status'        => 'active',
                    'stripe_id'     => null,
                    'paypal_id'     => $paypalSubId,
                    'comment'       => 'Recurring subscription via PayPal',
                ]);

                    Log::info('PayPal subscription ACTIVE — paypal_id: ' . $paypalSubId . ' ends_at: ' . $nextBillingDate . ' reg: ' . ($isRegistration ? 'yes' : 'no'));

                // ✅ Subscription ke liye entries NAHI banao
                // Entries PAYMENT.SALE.COMPLETED webhook se banegi
                    $numberOfFreeEntries = null;

                    if (function_exists('logUserActivity')) {
                        $previousSubscriptionCount = UserPackageSubscription::where('user_id', $user->id)
                        ->where('type', 'subscription')->count();

                        logUserActivity(
                            $user->id,
                            $previousSubscriptionCount > 1 ? 'subscription_resubscribed' : 'subscription_created',
                            ($previousSubscriptionCount > 1 ? 'User re-subscribed' : 'User subscribed') . ' via PayPal: ' . $plan->name,
                            [
                                'plan_id'                => $plan->id,
                                'plan_name'              => $plan->name,
                                'paypal_subscription_id' => $paypalSubId,
                                'subscription_record_id' => $userPackageSubscription->id,
                                'ends_at'                => $nextBillingDate,
                                'is_registration'        => $isRegistration,
                            ]
                        );
                    }

                    if (class_exists(PurchaseConfirmationNotification::class)) {
                        $user->notify(new PurchaseConfirmationNotification($user, $userPackageSubscription, $numberOfFreeEntries));
                    }

                    session()->forget([
                        'paypal_plan_id', 'paypal_type', 'paypal_subscription_id',
                        'paypal_is_upsell', 'paypal_reg_user_id',
                    ]);

                    $successMsg = 'Subscription activated successfully via PayPal!';

                    if ($isRegistration) {
                        $route = $thankYouRoutes[$plan->id] ?? 'thank_you';
                        return redirect()->route($route)->with('success', $successMsg);
                    }

                    return redirect()->route('subscription.manage')->with('success', $successMsg);
                }

                Log::error('PayPal Subscription status not ACTIVE: ' . ($details['status'] ?? 'unknown'), $details);
                return redirect()->route('subscription.manage')
                ->with('error', 'PayPal subscription is not active yet. Please try again.');
            }
        }

    /**
     * User ne PayPal pe Cancel kiya
     */
    public function cancel()
    {
        session()->forget([
            'paypal_plan_id', 'paypal_type', 'paypal_order_id',
            'paypal_subscription_id', 'paypal_is_upsell',
            'upsell_active', 'upsell_plan_id', 'upsell_expires',
        ]);
        return redirect()->route('subscription.manage')->with('error', 'PayPal payment was cancelled.');
    }

    /**
     * Helper: subscription ID extract karo
     */
    private function resolveSubscriptionId(array $resource): ?string
    {
        $subId = $resource['id'] ?? null;

        if (!$subId && isset($resource['links'])) {
            foreach ($resource['links'] as $link) {
                if (($link['rel'] ?? '') === 'self' && isset($link['href'])) {
                    $subId = basename(parse_url($link['href'], PHP_URL_PATH));
                    break;
                }
            }
        }

        Log::info('PayPal webhook — resolved subscription ID: ' . ($subId ?? 'null'));
        return $subId ?: null;
    }

    /**
     * Helper: SMS send karo
     */
    private function sendSmsAlert(string|int $userId, string $message): void
    {
        try {
            $user = User::find($userId);
            if ($user && $user->mobile) {
                $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($user->mobile, env('DEFAULT_REGION'));
                if ($phoneInfo['valid']) {
                    \App\Services\SmsService::sendSms($phoneInfo['formatted'], $message);
                }
            }
        } catch (\Exception $e) {
            Log::error('SMS send failed for user ' . $userId . ': ' . $e->getMessage());
        }
    }

    /**
     * PayPal Webhook
     */
    public function webhook(Request $request)
    {
        $eventType = $request->input('event_type');
        $resource  = $request->input('resource', []);

        Log::info('PayPal Webhook received: ' . $eventType, ['resource_id' => $resource['id'] ?? 'unknown']);

        switch ($eventType) {

            case 'BILLING.SUBSCRIPTION.CREATED':
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
            $subId = $this->resolveSubscriptionId($resource);
            Log::info('PayPal ' . $eventType . ' — subId: ' . ($subId ?? 'unknown') . ' (handled in success())');
            break;

            // ✅ Har mahine payment pe — entry banao + ends_at extend karo
            case 'PAYMENT.SALE.COMPLETED':
            $subId = $resource['billing_agreement_id'] ?? null;
            Log::info('PayPal PAYMENT.SALE.COMPLETED — billing_agreement_id: ' . ($subId ?? 'none'));

            if ($subId) {
                $record = UserPackageSubscription::where('paypal_id', $subId)->first();
                if ($record) {

            // ✅ PayPal API se subscription details lo — next billing date ke liye
                    $provider = new PayPalClient;
                    $provider->setApiCredentials(config('paypal'));
                    $provider->getAccessToken();
                    $subDetails = $provider->showSubscriptionDetails($subId);

                    Log::info('PayPal subscription details: ' . json_encode($subDetails['billing_info'] ?? []));

            // ✅ Next billing date — API se lo
                    $nextBillingDate = isset($subDetails['billing_info']['next_billing_time'])
                    ? Carbon::parse($subDetails['billing_info']['next_billing_time'])
                    : null;

                    $plan  = Plan::find($record->plan_id);
                    $weeks = $plan->access_duration_weeks ?? 4;

            // ✅ next_billing_time mila to use karo, nahi to weeks se calculate karo
                    $newEndsAt = $nextBillingDate
                    ? $nextBillingDate
                    : Carbon::parse($record->ends_at)->addWeeks($weeks);

                    $record->update([
                        'status'  => 'active',
                        'ends_at' => $newEndsAt,
                    ]);

                    Log::info('ends_at updated — user: ' . $record->user_id . ' ends_at: ' . $newEndsAt . ' source: ' . ($nextBillingDate ? 'paypal_api' : 'fallback_weeks'));

                    try {
                        PrizeDrawEntry::create([
                            'user_id'                  => $record->user_id,
                            'package_subscriptions_id' => $record->id,
                            'entries_added'            => $weeks,
                            'type'                     => 'paid',
                        ]);
                        Log::info('PrizeDrawEntry created — user ' . $record->user_id . ' entries: ' . $weeks);
                    } catch (\Exception $e) {
                        Log::error('PrizeDrawEntry failed: ' . $e->getMessage());
                    }

                    if (function_exists('logUserActivity')) {
                        logUserActivity($record->user_id, 'paypal_payment_sale_completed', 'PayPal sale payment completed', [
                            'paypal_subscription_id' => $subId,
                            'sale_id'                => $resource['id'] ?? null,
                            'plan_id'                => $record->plan_id,
                            'new_ends_at'            => $newEndsAt,
                            'ends_at_source'         => $nextBillingDate ? 'paypal_api' : 'fallback',
                        ]);
                    }
                } else {
                    Log::warning('PayPal PAYMENT.SALE.COMPLETED — no DB record for: ' . $subId);
                }
            }
            break;

            // ✅ Renewal — sirf status update
            case 'BILLING.SUBSCRIPTION.RENEWED':
            $subId = $this->resolveSubscriptionId($resource);
            if (!$subId) { Log::warning('PayPal RENEWED — could not resolve ID'); break; }

            $record = UserPackageSubscription::where('paypal_id', $subId)->first();
            if (!$record) { Log::warning('PayPal RENEWED — no DB record for: ' . $subId); break; }

            $record->update(['status' => 'active']);

            if (function_exists('logUserActivity')) {
                logUserActivity($record->user_id, 'paypal_subscription_renewed', 'PayPal subscription renewed', [
                    'paypal_subscription_id' => $subId,
                    'plan_id'                => $record->plan_id,
                ]);
            }
            Log::info('PayPal subscription renewed — user ' . $record->user_id);
            break;

            case 'BILLING.SUBSCRIPTION.CANCELLED':
            case 'BILLING.SUBSCRIPTION.SUSPENDED':
            $subId = $this->resolveSubscriptionId($resource);
            if (!$subId) { Log::warning('PayPal ' . $eventType . ' — could not resolve ID'); break; }

            $record = UserPackageSubscription::where('paypal_id', $subId)->first();
            if (!$record) {
                Log::warning('PayPal ' . $eventType . ' — no DB record for: ' . $subId);
                Log::info('DB subscription paypal_ids: ' . json_encode(
                    UserPackageSubscription::where('type', 'subscription')->pluck('paypal_id')->toArray()
                ));
                break;
            }

            $record->update(['status' => 'canceled']);

            if (function_exists('logUserActivity')) {
                logUserActivity($record->user_id, 'paypal_subscription_cancelled', 'PayPal subscription cancelled/suspended', [
                    'paypal_subscription_id' => $subId,
                    'plan_id'                => $record->plan_id,
                    'event'                  => $eventType,
                ]);
            }

            $this->sendSmsAlert($record->user_id, "⚠️ Xhale: Your PayPal subscription has been cancelled. Resubscribe to stay in the draw 👉 " . env('APP_URL'));
            Log::info('PayPal subscription cancelled — user ' . $record->user_id);
            break;

            case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
            $subId = $this->resolveSubscriptionId($resource);
            if (!$subId) { Log::warning('PayPal PAYMENT.FAILED — could not resolve ID'); break; }

            $record = UserPackageSubscription::where('paypal_id', $subId)->first();
            if (!$record) { Log::warning('PayPal PAYMENT.FAILED — no DB record for: ' . $subId); break; }

            $record->update(['status' => 'past_due']);

            if (function_exists('logUserActivity')) {
                logUserActivity($record->user_id, 'paypal_subscription_payment_failed', 'PayPal subscription payment failed', [
                    'paypal_subscription_id' => $subId,
                    'plan_id'                => $record->plan_id,
                ]);
            }

            $this->sendSmsAlert($record->user_id, "⚠️ Xhale payment issue: Your PayPal payment didn't go through. Update details now to stay in the draw 👉 " . env('APP_URL'));
            Log::info('PayPal payment failed — user ' . $record->user_id);
            break;

            default:
            Log::warning('Unhandled PayPal webhook event: ' . $eventType);
            break;
        }

        return response()->json(['status' => 'ok']);
    }
}