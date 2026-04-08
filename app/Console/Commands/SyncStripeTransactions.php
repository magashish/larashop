<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Invoice as StripeInvoice;
use Stripe\Charge;
use App\Models\User;
use App\Models\Transaction;

class SyncStripeTransactions extends Command
{
    protected $signature = 'stripe:sync-transactions';
    protected $description = 'Sync ALL Stripe transactions';

    public function handle()
    {
        Stripe::setApiKey(config('cashier.secret'));
        $this->info('Starting Stripe sync...');
        $this->syncInvoices();
        $this->syncCharges();
        $this->info('Stripe sync completed successfully.');
    }

    private function syncInvoices()
    {
        $this->info('Syncing invoices...');

        $invoices = StripeInvoice::all([
            'limit'  => 100,
            'expand' => ['data.charge'],
        ]);

        foreach ($invoices->autoPagingIterator() as $invoice) {

            // SKIP if already exists
            if (Transaction::where('stripe_object_id', $invoice->id)->exists()) {
                continue;
            }

            $user    = User::where('stripe_id', $invoice->customer)->first();
            $billing = $invoice->customer_address ?? null;

            // GET STRIPE FEE
            $stripeFee = null;
            $netAmount = null;
            if ($invoice->charge && $invoice->charge->balance_transaction) {
                $balanceTxn = \Stripe\BalanceTransaction::retrieve(
                    is_string($invoice->charge->balance_transaction)
                        ? $invoice->charge->balance_transaction
                        : $invoice->charge->balance_transaction->id
                );
                $stripeFee = $balanceTxn->fee / 100;
                $netAmount = $balanceTxn->net / 100;
            }

            $chargeId = null;
            $receiptUrl = null;
            $paymentMethodType = null;

            if ($invoice->charge) {
                $chargeId          = is_string($invoice->charge) ? $invoice->charge : $invoice->charge->id;
                $receiptUrl        = is_string($invoice->charge) ? null : $invoice->charge->receipt_url;
                $paymentMethodType = is_string($invoice->charge) ? null : $invoice->charge->payment_method_details?->type;
            }

            Transaction::create([
                'stripe_object_id'         => $invoice->id,
                'object_type'              => 'invoice',
                'stripe_invoice_id'        => $invoice->id,
                'stripe_payment_intent_id' => $invoice->payment_intent,
                'stripe_charge_id'         => $chargeId,
                'stripe_subscription_id'   => $invoice->subscription,
                'stripe_customer_id'       => $invoice->customer,
                'user_id'                  => $user?->id,

                'type'                     => 'subscription',
                'product_name'             => $invoice->lines->data[0]->description ?? null,
                'description'              => $invoice->description,
                'quantity'                 => $invoice->lines->data[0]->quantity ?? 1,
                'price_id'                 => $invoice->lines->data[0]->price->id ?? null,

                'amount'                   => $invoice->amount_paid / 100,
                'amount_refunded'          => $invoice->amount_remaining / 100,
                'stripe_fee'               => $stripeFee,
                'net_amount'               => $netAmount,
                'currency'                 => $invoice->currency,

                'status'                   => $invoice->status,
                'paid_at'                  => $invoice->status_transitions->paid_at
                                                ? date('Y-m-d H:i:s', $invoice->status_transitions->paid_at)
                                                : null,

                'billing_name'             => $invoice->customer_name,
                'billing_email'            => $invoice->customer_email,
                'billing_city'             => $billing->city ?? null,
                'billing_state'            => $billing->state ?? null,
                'billing_country'          => $billing->country ?? null,
                'billing_postcode'         => $billing->postal_code ?? null,

                'payment_method_type'      => $paymentMethodType,
                'receipt_url'              => $receiptUrl,
                'raw_data'                 => $invoice->toArray(),
            ]);
        }

        $this->info('Invoices synced.');
    }

    private function syncCharges()
    {
        $this->info('Syncing charges...');

        $charges = Charge::all([
            'limit'  => 100,
            'expand' => ['data.balance_transaction'],
        ]);

        foreach ($charges->autoPagingIterator() as $charge) {
            // Skip charges that belong to invoices
            if ($charge->invoice) {
                continue;
            }

            // Skip if already exists
            if (Transaction::where('stripe_object_id', $charge->id)->exists()) {
                continue;
            }

            $user    = User::where('stripe_id', $charge->customer)->first();
            $billing = $charge->billing_details->address ?? null;

            $stripeFee = null;
            $netAmount = null;
            if ($charge->balance_transaction) {
                $stripeFee = $charge->balance_transaction->fee / 100;
                $netAmount = $charge->balance_transaction->net / 100;
            }

            Transaction::create([
                'stripe_object_id'         => $charge->id,
                'object_type'              => 'charge',
                'stripe_charge_id'         => $charge->id,
                'stripe_payment_intent_id' => $charge->payment_intent,
                'stripe_customer_id'       => $charge->customer,
                'user_id'                  => $user?->id,

                'type'                     => 'one_time',
                'description'              => $charge->description,
                'quantity'                 => 1,

                'amount'                   => $charge->amount / 100,
                'amount_refunded'          => $charge->amount_refunded / 100,
                'stripe_fee'               => $stripeFee,
                'net_amount'               => $netAmount,
                'currency'                 => $charge->currency,

                'status'                   => $charge->status,
                'paid_at'                  => $charge->created
                                                ? date('Y-m-d H:i:s', $charge->created)
                                                : null,
                'refunded_at'              => $charge->refunded && $charge->amount_refunded > 0
                                                ? now()
                                                : null,

                'billing_name'             => $charge->billing_details->name ?? null,
                'billing_email'            => $charge->billing_details->email ?? null,
                'billing_city'             => $billing->city ?? null,
                'billing_state'            => $billing->state ?? null,
                'billing_country'          => $billing->country ?? null,
                'billing_postcode'         => $billing->postal_code ?? null,

                'payment_method_type'      => $charge->payment_method_details->type ?? null,
                'receipt_url'              => $charge->receipt_url ?? null,
                'raw_data'                 => $charge->toArray(),
            ]);
        }

        $this->info('Charges synced.');
    }
}