<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Illuminate\Support\Facades\Log; // Import the Log facade for debugging
use Illuminate\Support\Carbon; // Ensure Carbon is imported if used in handlers
use Stripe\Subscription as StripeSubscription; // Ensure this is imported if used in handlers
use Laravel\Cashier\Payment; // Import if you use Payment objects, like in invoice.payment_action_required
use Illuminate\Notifications\Notifiable; // Import if you use notifications


// Import your custom models
use App\Models\UserPackageSubscription; // Your custom subscription table model
use App\Models\Plan; // Your Plan model to map Stripe Price IDs to your internal plan_id
use App\Models\User;
use App\Models\PrizeDrawEntry;


use App\Services\SmsService;
use App\Helpers\PhoneHelper;
use Exception;


// If you have custom notifications, make sure to import them
// For example:
// use App\Notifications\SubscriptionUpdated;
// use App\Notifications\SubscriptionCancelled;
// use App\Notifications\PaymentActionRequired;
// use Illuminate\Support\Facades\Mail; // If you're sending emails directly

class StripeWebhookController extends CashierController
{
    /**
     * Handle customer subscription created.
     *
     * This is a copy of the default Cashier handler.
     * You can add your custom logic here if needed.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $data = $payload['data']['object'];

            if (! $user->subscriptions->contains('stripe_id', $data['id'])) {
                if (isset($data['trial_end'])) {
                    $trialEndsAt = Carbon::createFromTimestamp($data['trial_end']);
                } else {
                    $trialEndsAt = null;
                }

                $firstItem = $data['items']['data'][0];
                $isSinglePrice = count($data['items']['data']) === 1;

                $subscription = $user->subscriptions()->create([
                    'type' => $data['metadata']['type'] ?? $data['metadata']['name'] ?? $this->newSubscriptionType($payload),
                    'stripe_id' => $data['id'],
                    'stripe_status' => $data['status'],
                    'stripe_price' => $isSinglePrice ? $firstItem['price']['id'] : null,
                    'quantity' => $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null,
                    'trial_ends_at' => $trialEndsAt,
                    'ends_at' => null,
                ]);

                foreach ($data['items']['data'] as $item) {
                    $subscription->items()->create([
                        'stripe_id' => $item['id'],
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }
            }

            // Terminate the billable's generic trial if it exists...
            if (! is_null($user->trial_ends_at)) {
                $user->trial_ends_at = null;
                $user->save();
            }
        }
        $data = $payload['data']['object'];
        $firstItem = $data['items']['data'][0];
        $isSinglePrice = count($data['items']['data']) === 1;
      // **** CUSTOM LOGIC FOR user_package_subscriptions TABLE ****

        // try {
        //     $plan = Plan::findByStripePriceId($firstItem['price']['id']); 
        //     Log::info('Webhook Debug (Created): Result of Plan::findByStripePriceId for ' . $firstItem['price']['id'] . ': ' . ($plan ? 'Found (ID: ' . $plan->id . ')' : 'Not Found'));

        //     if ($plan) {
        //         UserPackageSubscription::create([
        //             'user_id' => $user->id,
        //             'plan_id' => $plan->id,
        //             'type' => $data['metadata']['custom_type'] ?? 'subscription', 
        //             'purchase_date' => Carbon::now(), 
        //             'starts_at' => Carbon::createFromTimestamp($data['current_period_start']),
        //             'ends_at' => $trialEndsAt ?? (isset($data['current_period_end']) ? Carbon::createFromTimestamp($data['current_period_end']) : null),
        //             'status' => 'active', 
        //             'stripe_id' => $data['id'],
        //         ]);
        //         Log::info('Custom table: UserPackageSubscription created for user ' . $user->id . ' with Stripe ID ' . $data['id']);
        //     } else {
        //         Log::warning('Custom table: Plan not found for Stripe Price ID: ' . $firstItem['price']['id'] . '. UserPackageSubscription was NOT created.');
        //     }
        // } catch (\Exception $e) {
        //     Log::error('Custom table: Error creating UserPackageSubscription: ' . $e->getMessage());
        // }




                return $this->successMethod();
            }

    /**
     * Handle customer subscription updated.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionUpdated(array $payload)
    {


       //Log::info('Webhook received: customer.subscription.updated', $payload);

        // --- Extract Relevant Data from Webhook Payload ---
        $stripeSubscriptionObject = $payload['data']['object'] ?? null;
        if (!$stripeSubscriptionObject) {
            Log::error('Webhook payload is missing the data.object for customer.subscription.updated.', $payload);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $stripeSubscriptionId = $stripeSubscriptionObject['id'] ?? null; 
        $stripeCustomerId = $stripeSubscriptionObject['customer'] ?? null; 
        $stripePlanId = $stripeSubscriptionObject['plan']['id'] ?? null; 
        $currentPeriodStart = $stripeSubscriptionObject['current_period_start'] ?? null; 
        $currentPeriodEnd = $stripeSubscriptionObject['current_period_end'] ?? null;  
        $status = $stripeSubscriptionObject['status'] ?? null;
        $quantity = $stripeSubscriptionObject['quantity'] ?? 1; 


        $user = User::where('stripe_id', $stripeCustomerId)->first();
        $stripePlan = Plan::where('stripe_plan', $stripePlanId)->first();


        Log::info('Webhook received: customer.subscription.updated', [
            'payload_raw_data_1' => 'yes', // Consider logging the full payload for deep debugging, but be mindful of log size
            'extracted_data' => [
                'stripeSubscriptionId' => $stripeSubscriptionId,
                'stripeCustomerId' => $stripeCustomerId,
                'stripePlanId' => $stripePlanId,
                'currentPeriodStart' => $currentPeriodStart,
                'currentPeriodEnd' => $currentPeriodEnd,
                'status' => $status,
                'quantity' => $quantity,
            ]
        ]);

        $startsAt = Carbon::createFromTimestamp($currentPeriodStart);
        $endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        $subscriptionData = [
            'user_id'   => $user->id,
            'plan_id'   => $stripePlan->id,
            'starts_at' => $startsAt,      
            'ends_at' => $endsAt,           
            'status' => $status, 
            'type' => 'subscription',                  
        ];

        $subscription = UserPackageSubscription::updateOrCreate(
            [
                'stripe_id' => $stripeSubscriptionId, 
            ],
            $subscriptionData
        );


        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $data = $payload['data']['object'];

            $subscription = $user->subscriptions()->firstOrNew(['stripe_id' => $data['id']]);

            if (
                isset($data['status']) &&
                $data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED
            ) {
                $subscription->items()->delete();
                $subscription->delete();
                Log::info('Subscription marked as incomplete_expired and deleted.');
                return;
            }

            $subscription->type = $subscription->type ?? $data['metadata']['type'] ?? $data['metadata']['name'] ?? $this->newSubscriptionType($payload);

            $firstItem = $data['items']['data'][0];
            $isSinglePrice = count($data['items']['data']) === 1;

            Log::info('Webhook Debug: isSinglePrice = ' . ($isSinglePrice ? 'true' : 'false'));
            Log::info('Webhook Debug: firstItem quantity from payload = ' . ($firstItem['quantity'] ?? 'NULL/Not Set'));

            // Price...
            $subscription->stripe_price = $isSinglePrice ? $firstItem['price']['id'] : null;

            // Quantity...
            // This updates the 'quantity' column on the main 'subscriptions' table
            $subscription->quantity = $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

            Log::info('Webhook Debug: $subscription->quantity BEFORE main save = ' . ($subscription->quantity ?? 'NULL'));


            // Trial ending date...
            if (isset($data['trial_end'])) {
                $trialEnd = Carbon::createFromTimestamp($data['trial_end']);

                if (! $subscription->trial_ends_at || $subscription->trial_ends_at->ne($trialEnd)) {
                    $subscription->trial_ends_at = $trialEnd;
                }
            }

            // Cancellation date...
            if ($data['cancel_at_period_end'] ?? false) {
                $subscription->ends_at = $subscription->onTrial()
                ? $subscription->trial_ends_at
                : Carbon::createFromTimestamp($data['current_period_end']);
            } elseif (isset($data['cancel_at']) || isset($data['canceled_at'])) {
                $subscription->ends_at = Carbon::createFromTimestamp($data['cancel_at'] ?? $data['canceled_at']);
            } else {
                $subscription->ends_at = null;
            }

            // Status...
            if (isset($data['status'])) {
                $subscription->stripe_status = $data['status'];
            }

            $subscription->save(); // Save the changes to the main subscription record

            Log::info('Webhook Debug: Main subscription saved. Now processing items.');

            // Update subscription items...
            if (isset($data['items'])) {
                $subscriptionItemIds = [];

                foreach ($data['items']['data'] as $item) {
                    $subscriptionItemIds[] = $item['id'];

                    Log::info('Webhook Debug: Processing subscription item: ' . $item['id'] . ' with quantity: ' . ($item['quantity'] ?? 'NULL/Not Set'));

                    // This updates the 'quantity' column on the 'subscription_items' table
                    $subscription->items()->updateOrCreate([
                        'stripe_id' => $item['id'],
                    ], [
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }

                // Delete items that aren't attached to the subscription anymore...
                $subscription->items()->whereNotIn('stripe_id', $subscriptionItemIds)->delete();
            }

            // Custom logic for when a subscription is updated
            // Example: Send an email about the updated subscription details
            // Log::info('Custom logic: Subscription updated for user ' . $user->id);
            // $user->notify(new SubscriptionUpdatedNotification($subscription));
        }

        return $this->successMethod();
    }

    /**
     * Handle the cancellation of a customer subscription.
     *
     * This is a copy of the default Cashier handler.
     * You can add your custom logic here if needed.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    // protected function handleCustomerSubscriptionDeleted(array $payload)
    // {
    //     Log::info('Webhook received: customer.subscription.deleted', $payload);

    //     if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
    //         $user->subscriptions->filter(function ($subscription) use ($payload) {
    //             return $subscription->stripe_id === $payload['data']['object']['id'];
    //         })->each(function ($subscription) {
    //             $subscription->skipTrial()->markAsCanceled();
    //             Log::info('Subscription ' . $subscription->stripe_id . ' marked as canceled for user ' . $subscription->user_id);
    //         });

    //         // Custom logic for when a subscription is deleted/cancelled
    //         // Example: Revoke access to features, send a cancellation survey
    //         // Log::info('Custom logic: Subscription deleted for user ' . $user->id);
    //         // $user->notify(new SubscriptionCancelledNotification($user));
    //     }

    //     return $this->successMethod();
    // }


protected function handleCustomerSubscriptionDeleted(array $payload)
{
    Log::info('Webhook received: customer.subscription.deleted', $payload);

    $stripeSubscriptionObject = $payload['data']['object'] ?? null;
    if (!$stripeSubscriptionObject) {
        Log::error('Webhook payload is missing data.object for customer.subscription.deleted.', $payload);
        return response()->json(['message' => 'Invalid payload'], 400);
    }

    // ✅ Extract details safely
    $stripeSubscriptionId = $stripeSubscriptionObject['id'] ?? null;
    $stripeCustomerId     = $stripeSubscriptionObject['customer'] ?? null;
    $currentPeriodStart   = $stripeSubscriptionObject['current_period_start'] ?? null;
    $currentPeriodEnd     = $stripeSubscriptionObject['current_period_end'] ?? null;
    $status               = $stripeSubscriptionObject['status'] ?? 'canceled';

    Log::info("Processing deletion for subscription: {$stripeSubscriptionId}, customer: {$stripeCustomerId}");

    // ✅ Find the user by Stripe customer ID
    if ($user = $this->getUserByStripeId($stripeCustomerId)) {

        $user->subscriptions
            ->filter(fn($subscription) => $subscription->stripe_id === $stripeSubscriptionId)
            ->each(function ($subscription) use ($status, $stripeSubscriptionId) {
                // Cancel Laravel Cashier subscription
                $subscription->skipTrial()->markAsCanceled();

                Log::info("Subscription {$stripeSubscriptionId} marked as canceled for user {$subscription->user_id}");

                // ✅ Update your custom UserPackageSubscription table
                $subscriptionData = ['status' => $status];

                $updatedSubscription = UserPackageSubscription::updateOrCreate(
                    [
                        'stripe_id' => $stripeSubscriptionId,
                        'user_id'   => $subscription->user_id,
                    ],
                    $subscriptionData
                );

                Log::info("UserPackageSubscription updated for deleted Stripe subscription", [
                    'stripe_id' => $stripeSubscriptionId,
                    'user_id'   => $subscription->user_id,
                    'status'    => $status,
                ]);
            });

    } else {
        Log::warning("No user found for Stripe customer ID: {$stripeCustomerId}");
    }

    return $this->successMethod();
}



    /**
     * Handle customer updated.
     *
     * This is a copy of the default Cashier handler.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerUpdated(array $payload)
    {
        Log::info('Webhook received: customer.updated', $payload);

        if ($user = $this->getUserByStripeId($payload['data']['object']['id'])) {
            $user->updateDefaultPaymentMethodFromStripe();
            Log::info('Default payment method updated for user ' . $user->id);
        }

        return $this->successMethod();
    }

    /**
     * Handle deleted customer.
     *
     * This is a copy of the default Cashier handler.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerDeleted(array $payload)
    {
        Log::info('Webhook received: customer.deleted', $payload);

        if ($user = $this->getUserByStripeId($payload['data']['object']['id'])) {
            $user->subscriptions->each(function (\Laravel\Cashier\Subscription $subscription) {
                $subscription->skipTrial()->markAsCanceled();
                Log::info('Subscription ' . $subscription->stripe_id . ' marked as canceled due to customer deletion.');
            });

            $user->forceFill([
                'stripe_id' => null,
                'trial_ends_at' => null,
                'pm_type' => null,
                'pm_last_four' => null,
            ])->save();
            Log::info('User ' . $user->id . ' Stripe data cleared due to customer deletion.');
        }

        return $this->successMethod();
    }

    /**
     * Handle payment method automatically updated by vendor.
     *
     * This is a copy of the default Cashier handler.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentMethodAutomaticallyUpdated(array $payload)
    {
        Log::info('Webhook received: payment_method.automatically_updated', $payload);

        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $user->updateDefaultPaymentMethodFromStripe();
            Log::info('Payment method automatically updated for user ' . $user->id);
        }

        return $this->successMethod();
    }

    /**
     * Handle payment action required for invoice.
     *
     * This is a copy of the default Cashier handler.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoicePaymentActionRequired(array $payload)
    {
        Log::info('Webhook received: invoice.payment_action_required', $payload);

        if (is_null($notification = config('cashier.payment_notification'))) {
            return $this->successMethod();
        }

        if ($payload['data']['object']['metadata']['is_on_session_checkout'] ?? false) {
            return $this->successMethod();
        }

        if ($payload['data']['object']['subscription_details']['metadata']['is_on_session_checkout'] ?? false) {
            return $this->successMethod();
        }

        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            if (in_array(Notifiable::class, class_uses_recursive($user))) {
                $payment = new Payment($user->stripe()->paymentIntents->retrieve(
                    $payload['data']['object']['payment_intent']
                ));

                $user->notify(new $notification($payment));
                Log::info('Payment action required notification sent for user ' . $user->id);
            }
        }

        return $this->successMethod();
    }

        /**
     * Handle invoice payment succeeded.
     *
     * This webhook is sent when a payment for an invoice (often a subscription renewal) is successful.
     * Cashier typically handles updating internal subscription details like 'last_bill_at'.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
        // protected function handleInvoicePaymentSucceeded_old(array $payload)
        // {
        //     Log::info('Webhook received: invoice.payment_succeeded');

        //     $invoice = $payload['data']['object'];
        //     $customerId = $invoice['customer'];
        // $amountPaid = $invoice['amount_paid'] / 100; // Amount in cents, convert to your currency unit
        // $invoiceId = $invoice['id'];
        // $subscriptionId = $invoice['subscription'] ?? null; // May not be present for one-off invoices

        // Log::info("Custom logic: Payment succeeded for customer {$customerId}. Amount: {$amountPaid}. Invoice: {$invoiceId}. Subscription: {$subscriptionId}");


        // // --- Extract Relevant Data from Webhook Payload ---
        // $stripeSubscriptionObject = $payload['data']['object'] ?? null;
        // if (!$stripeSubscriptionObject) {
        //     Log::error('Webhook payload is missing the data.object for invoice.payment_succeeded.');
        //     return response()->json(['message' => 'Invalid payload'], 400);
        // }

        // $stripeSubscriptionId = $stripeSubscriptionObject['id'] ?? null; 
        // $stripeCustomerId = $stripeSubscriptionObject['customer'] ?? null; 
        // $stripePlanId = $stripeSubscriptionObject['plan']['id'] ?? null; 
        // $currentPeriodStart = $stripeSubscriptionObject['current_period_start'] ?? null; 
        // $currentPeriodEnd = $stripeSubscriptionObject['current_period_end'] ?? null;  
        // $status = $stripeSubscriptionObject['status'] ?? null;
        // $quantity = $stripeSubscriptionObject['quantity'] ?? 1; 

        // Log::info('Webhook received: invoice.payment_succeeded', [
        //     'payload_raw_data_2' => 'yes', // Consider logging the full payload for deep debugging, but be mindful of log size
        //     'extracted_data' => [
        //         'stripeSubscriptionId' => $stripeSubscriptionId,
        //         'stripeCustomerId' => $stripeCustomerId,
        //         'stripePlanId' => $stripePlanId,
        //         'currentPeriodStart' => $currentPeriodStart,
        //         'currentPeriodEnd' => $currentPeriodEnd,
        //         'status' => $status,
        //         'quantity' => $quantity,
        //     ]
        // ]);

        // $startsAt = Carbon::createFromTimestamp($currentPeriodStart);
        // $endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        // $subscriptionData = [
        //     'starts_at' => $startsAt,      
        //     'ends_at' => $endsAt,           
        //     'status' => $status,                  
        // ];

        // $subscription = UserPackageSubscription::updateOrCreate(
        //     [
        //         'stripe_id' => $stripeSubscriptionId, 
        //     ],
        //     $subscriptionData
        // );

        // $user = User::where('stripe_id', $stripeCustomerId)->first();
        // $stripePlan = Plan::where('stripe_plan', $stripePlanId)->first();

        // Log::info('Webhook received: invoice.payment_succeeded', [
        //     'payload_raw_data_2' => 'yes', // Consider logging the full payload for deep debugging, but be mindful of log size
        //     'extracted_data' => [
        //         'user' => $user,
        //         'stripePlan' => $stripePlan,
        //     ]
        // ]);


        //   // 2. PrizeDrawEntry creation (NEW LOGIC)
        //     try {
        //         PrizeDrawEntry::create([
        //             'user_id' => $user->id,
        //             'package_subscriptions_id'  => $subscription->id,
        //              'entries_added'             => 1, 
        //              'type'             => 'paid', 
        //         ]);
        //         Log::info('PrizeDrawEntry created successfully for user ' . $user->id);
        //     } catch (\Exception $e) {
        //         Log::error('Error creating PrizeDrawEntry: ' . $e->getMessage());
        //     }


        //     return $this->successMethod();
        // }



        protected function handleInvoicePaymentSucceeded(array $payload)
        {
    // The Invoice is the main object in this webhook event.
            $invoice = $payload['data']['object'];
            $stripeCustomerId = $invoice['customer'] ?? null;
    // CRITICAL: The ID we need for user_package_subscriptions is the 'subscription' ID (sub_...), not the invoice 'id' (in_...).
            $subscriptionId = $invoice['subscription'] ?? null;
            $amountPaid = $invoice['amount_paid'] / 100;
            $invoiceId = $invoice['id'];

            Log::info('Webhook received: invoice.payment_succeeded');
            Log::info("Custom logic: Payment succeeded for customer {$stripeCustomerId}. Amount: {$amountPaid}. Invoice: {$invoiceId}. Subscription: {$subscriptionId}");



              // --- Extract Relevant Data from Webhook Payload ---
        $stripeSubscriptionObject = $payload['data']['object'] ?? null;
        if (!$stripeSubscriptionObject) {
            Log::error('Webhook payload is missing the data.object for invoice.payment_succeeded.');
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $stripeSubscriptionId = $stripeSubscriptionObject['id'] ?? null; 
        $stripeCustomerId = $stripeSubscriptionObject['customer'] ?? null; 
        $stripePlanId = $stripeSubscriptionObject['plan']['id'] ?? null; 
        $currentPeriodStart = $stripeSubscriptionObject['current_period_start'] ?? null; 
        $currentPeriodEnd = $stripeSubscriptionObject['current_period_end'] ?? null;  
        $status = $stripeSubscriptionObject['status'] ?? null;
        $quantity = $stripeSubscriptionObject['quantity'] ?? 1; 

            $isPaidAndActive = in_array($status, ['paid']);
           $finalStatus = $isPaidAndActive ? 'active' : $status; 


            // --- 1. Find User ---
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if (!$user) {
                Log::error("User not found for Stripe Customer ID: {$stripeCustomerId}. Cannot process subscription.");
                return $this->successMethod();
            }

            if (!$subscriptionId) {
                Log::info('One-off payment detected (no subscription ID). Skipping subscription update.');
                return $this->successMethod();
            }

            // Use invoice period for subscription dates
            $currentPeriodStart = $invoice['period_start'] ?? time();
            $currentPeriodEnd = $invoice['period_end'] ?? time();

            $startsAt = Carbon::createFromTimestamp($currentPeriodStart);
            $endsAt = Carbon::createFromTimestamp($currentPeriodEnd);

              // --- 2. Fix the missing user_id error using updateOrCreate ---
            $subscriptionData = [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $finalStatus,
               // The plan/package ID would typically be derived from the invoice line items if needed
            ];

            $subscription = UserPackageSubscription::updateOrCreate(
                [
            // Search criteria: CRITICAL to include user_id here.
                    'stripe_id' => $subscriptionId,
                    'user_id' => $user->id,
                ],
                $subscriptionData
            );

            Log::info('Custom table: UserPackageSubscription synced successfully for sub ID ' . $subscriptionId);

             // --- 3. PrizeDrawEntry creation (NEW LOGIC) ---
            try {
                PrizeDrawEntry::create([
                    'user_id' => $user->id,
                    'package_subscriptions_id' => $subscription->id,
                    'entries_added' => 1,
                    'type' => 'paid'
        ]);
                Log::info('PrizeDrawEntry created successfully for user ' . $user->id);
            } catch (\Exception $e) {
                Log::error('Error creating PrizeDrawEntry: ' . $e->getMessage());
            }

            return $this->successMethod();
        }







      /**
     * Handle invoice payment failed.
     *
     * This webhook is sent when a payment for an invoice (often a subscription renewal) fails.
     * Cashier typically updates the subscription's 'stripe_status' (e.g., to 'past_due').
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
      protected function handleInvoicePaymentFailed_old(array $payload)
      {
        // Use error level for failures, as these require attention.
        Log::error('Webhook received: invoice.payment_failed');

        $invoice = $payload['data']['object'];
        $customerId = $invoice['customer'];
        $invoiceAmount = $invoice['amount_due'] / 100; // Amount is in cents
        $invoiceId = $invoice['id'];
        $subscriptionId = $invoice['subscription'] ?? null;
        $failureReason = $invoice['last_payment_error']['message'] ?? 'Unknown reason';
        $failureCode = $invoice['last_payment_error']['code'] ?? 'N/A';
        $failureType = $invoice['last_payment_error']['type'] ?? 'N/A';

        Log::error("Custom logic: Payment failed for customer {$customerId}. Amount: {$invoiceAmount}. Invoice: {$invoiceId}. Subscription: {$subscriptionId}. Reason: {$failureReason} (Code: {$failureCode}, Type: {$failureType})");

        $stripeSubscriptionObject = $payload['data']['object'] ?? null;
        if (!$stripeSubscriptionObject) {
            Log::error('Webhook payload is missing the data.object for invoice.payment_failed.');
            return response()->json(['message' => 'Invalid payload'], 400);
        }


           

        $stripeSubscriptionId = $stripeSubscriptionObject['id'] ?? null; 
        $stripeCustomerId = $stripeSubscriptionObject['customer'] ?? null; 
        $stripePlanId = $stripeSubscriptionObject['plan']['id'] ?? null; 
        $currentPeriodStart = $stripeSubscriptionObject['current_period_start'] ?? null; 
        $currentPeriodEnd = $stripeSubscriptionObject['current_period_end'] ?? null;  
        $status = $stripeSubscriptionObject['status'] ?? null;
        $quantity = $stripeSubscriptionObject['quantity'] ?? 1; 

        Log::info('Webhook received: invoice.payment_failed', [
            'payload_raw_data_3' => 'yes', // Consider logging the full payload for deep debugging, but be mindful of log size
            'extracted_data' => [
                'stripeSubscriptionId' => $stripeSubscriptionId,
                'stripeCustomerId' => $stripeCustomerId,
                'stripePlanId' => $stripePlanId,
                'currentPeriodStart' => $currentPeriodStart,
                'currentPeriodEnd' => $currentPeriodEnd,
                'status' => $status,
                'quantity' => $quantity,
            ]
        ]);

        $startsAt = Carbon::createFromTimestamp($currentPeriodStart);
        $endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        $subscriptionData = [
            'starts_at' => $startsAt,      
            'ends_at' => $endsAt,           
            'status' => $status,                  
        ];

        $subscription = UserPackageSubscription::updateOrCreate(
            [
                'stripe_id' => $stripeSubscriptionId, 
            ],
            $subscriptionData
        );


           $user = User::where('stripe_id', $stripeCustomerId)->first();
            if (!$user) {
                Log::error("User not found for Stripe Customer ID: {$stripeCustomerId}. Cannot process subscription.");
                return $this->successMethod();
            }



            try {
                $rawPhone = $user->mobile; 
                $defaultRegion = env('DEFAULT_REGION');
                $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($rawPhone, $defaultRegion);
                if (!$phoneInfo['valid']) {
                    return $this->error("❌ Invalid phone number for user {$user->email}: {$rawPhone}");
                    //continue;
                }

                $phoneNumber = $phoneInfo['formatted']; 
                $country = $phoneInfo['country'];

                $link = env('APP_URL');

                 $message = "⚠️ Xhale payment issue: Your payment didn’t go through (card declined/insufficient funds). "
             . "Update details now to stay in the draw 👉 {$link}";

                $response = \App\Services\SmsService::sendSms($phoneNumber, $message);
                if ($response['status'] === 'success') {
                    $this->info("✅ SMS sent to {$phoneNumber} ({$country})");
                } else {
                    $this->error("⚠️ SMS failed for {$phoneNumber}: " . ($response['error'] ?? 'Unknown error'));
                }
            } catch (Exception $e) {
                $this->error("💥 Exception for {$user->email}: " . $e->getMessage());
            }





        // Always return a 200 OK response to Stripe so it knows the webhook was received.
            return $this->successMethod();
        }

         // -------------------------------------------------------
    // invoice.payment_failed
    // -------------------------------------------------------
    protected function handleInvoicePaymentFailed(array $payload)
    {
        Log::error('Webhook received: invoice.payment_failed');

        $invoice        = $payload['data']['object'];
        $customerId     = $invoice['customer']     ?? null;
        $subscriptionId = $invoice['subscription'] ?? null; // FIX: sub_... not in_...
        $invoiceAmount  = ($invoice['amount_due']  ?? 0) / 100;
        $invoiceId      = $invoice['id'] ?? null;
        $failureReason  = $invoice['last_payment_error']['message'] ?? 'Unknown reason';
        $failureCode    = $invoice['last_payment_error']['code']    ?? 'N/A';
        $failureType    = $invoice['last_payment_error']['type']    ?? 'N/A';

        Log::error("Custom logic: Payment failed for customer {$customerId}. Amount: {$invoiceAmount}. Invoice: {$invoiceId}. Subscription: {$subscriptionId}. Reason: {$failureReason} (Code: {$failureCode}, Type: {$failureType})");

        // FIX: find user BEFORE updateOrCreate
        $user = User::where('stripe_id', $customerId)->first();
        if (!$user) {
            Log::error("User not found for Stripe Customer ID: {$customerId}.");
            return $this->successMethod();
        }

        if (!$subscriptionId) {
            Log::warning("No subscription ID on invoice {$invoiceId}. Skipping update.");
            return $this->successMethod();
        }

        $currentPeriodStart = $invoice['period_start'] ?? time();
        $currentPeriodEnd   = $invoice['period_end']   ?? time();

        UserPackageSubscription::updateOrCreate(
            [
                'stripe_id' => $subscriptionId, // FIX: sub_... ID
                'user_id'   => $user->id,        // FIX: always include user_id
            ],
            [
                'starts_at' => Carbon::createFromTimestamp($currentPeriodStart),
                'ends_at'   => Carbon::createFromTimestamp($currentPeriodEnd),
                'status'    => $invoice['status'] ?? 'open',
            ]
        );

        Log::info("UserPackageSubscription updated for failed payment. Sub: {$subscriptionId}, User: {$user->id}");

        // FIX: use Log:: not $this->info()/$this->error()
        try {
            $phoneInfo = \App\Helpers\PhoneHelper::detectCountry($user->mobile, env('DEFAULT_REGION'));

            if (!$phoneInfo['valid']) {
                Log::error("Invalid phone for user {$user->email}: {$user->mobile}");
                return $this->successMethod();
            }

            $link    = env('APP_URL');
            $message = "⚠️ Xhale payment issue: Your payment didn't go through (card declined/insufficient funds). "
                . "Update details now to stay in the draw 👉 {$link}";

            $response = \App\Services\SmsService::sendSms($phoneInfo['formatted'], $message);

            if ($response['status'] === 'success') {
                Log::info("SMS sent to {$phoneInfo['formatted']} ({$phoneInfo['country']})");
            } else {
                Log::error("SMS failed for {$phoneInfo['formatted']}: " . ($response['error'] ?? 'Unknown'));
            }
        } catch (Exception $e) {
            Log::error("SMS exception for {$user->email}: " . $e->getMessage());
        }

        return $this->successMethod();
    }


    /**
     * Determines the type that should be used when new subscriptions are created from the Stripe dashboard.
     *
     * @param  array  $payload
     * @return string
     */
    protected function newSubscriptionType(array $payload)
    {
        return 'default';
    }

    /**
     * Get the customer instance by Stripe ID.
     *
     * @param  string|null  $stripeId
     * @return \Laravel\Cashier\Billable|null
     */
    protected function getUserByStripeId($stripeId)
    {
        // This method relies on Cashier's internal findBillable,
        // which uses the 'stripe_id' column on your Billable model (e.g., User).
        return \Laravel\Cashier\Cashier::findBillable($stripeId);
    }

    /**
     * Handle successful calls on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function successMethod($parameters = [])
    {
        // Stripe expects a 200 OK response to mark the webhook as successful.
        return new \Symfony\Component\HttpFoundation\Response('Webhook Handled', 200);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * This method is called if Cashier receives a webhook event for which
     * there isn't a corresponding `handleEventName` method.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function missingMethod($parameters = [])
    {
        Log::warning('Unhandled Stripe webhook event: ' . ($parameters['type'] ?? 'unknown_event_type'), $parameters);
        // It's generally good practice to return a 200 OK even for unhandled events
        // so Stripe doesn't keep retrying them.
        return new \Symfony\Component\HttpFoundation\Response;
    }

    /**
     * Set the number of automatic retries due to an object lock timeout from Stripe.
     *
     * @param  int  $retries
     * @return void
     */
    protected function setMaxNetworkRetries($retries = 3)
    {
        // This method configures the underlying Stripe PHP library's retry logic.
        \Stripe\Stripe::setMaxNetworkRetries($retries);
    }
}