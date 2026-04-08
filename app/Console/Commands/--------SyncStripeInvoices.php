<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Invoice as StripeInvoice;
use App\Models\User;
use App\Models\Invoice;

class SyncStripeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-stripe-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    Stripe::setApiKey(config('cashier.secret'));

    $invoices = StripeInvoice::all(['limit' => 100]);

    foreach ($invoices->autoPagingIterator() as $stripeInvoice) {

        if ($stripeInvoice->status !== 'paid') {
            continue;
        }

        $user = User::where('stripe_id', $stripeInvoice->customer)->first();

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'stripe_customer_id' => $stripeInvoice->customer,
                'user_id' => $user?->id,
                'amount_paid' => $stripeInvoice->amount_paid / 100,
                'amount_due' => $stripeInvoice->amount_due / 100,
                'currency' => $stripeInvoice->currency,
                'status' => $stripeInvoice->status,
                'invoice_pdf' => $stripeInvoice->invoice_pdf,
                'hosted_invoice_url' => $stripeInvoice->hosted_invoice_url,
                'paid_at' => date('Y-m-d H:i:s', $stripeInvoice->created),
            ]
        );
    }

    $this->info('All Stripe invoices synced successfully.');
}
}
