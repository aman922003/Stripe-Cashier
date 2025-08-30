<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Subscription as ModelSubscription;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use Stripe\Invoice;
use Stripe\Subscription as StripeSubscription;
use Exception;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Stripe::setApiKey(config('cashier.secret'));

        $user = $request->user();

        // Return an empty collection if user has no Stripe ID
        if (!$user->stripe_id) {
            return view('transactions.index', ['invoices' => collect()]);
        }

        // Fetch invoices for this Stripe customer
        $stripeInvoices = Invoice::all([
            'customer' => $user->stripe_id,
            'limit' => 10,
        ]);

        return view('transactions.index', [
            'invoices' => collect($stripeInvoices->data),
        ]);
    }

    // public function refund($invoiceId, Request $request)
    // {
    //     try {
    //         // Stripe::setApiKey(config('cashier.secret'));

    //         // Find the invoice by its ID
    //         $invoice = Invoice::retrieve($invoiceId);

    //         // Check if the invoice is already refunded or has been voided
    //         if ($invoice->status == 'uncollectible' || $invoice->status == 'void') {
    //             return back()->withErrors(['error' => 'This invoice cannot be refunded.']);
    //         }

    //         // Create the refund for the payment
    //         $refund = Refund::create([
    //             'charge' => $invoice->charge, // The charge ID from the invoice
    //         ]);

    //         // Refund the payment and cancel the subscription if necessary
    //         if ($refund) {
    //             // Find the subscription associated with the invoice in the local database
    //             $subscription = Subscription::where('stripe_id', $invoice->subscription)->first();

    //             if ($subscription) {
    //                 // Cancel the subscription on Stripe
    //                 $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_id);
    //                 $stripeSubscription->cancel();

    //                 // Update the local subscription status
    //                 $subscription->status = 'canceled';
    //                 $subscription->save();
    //             }

    //             // Save refund details in the local database (optional)
    //             $transaction = new Transaction();
    //             $transaction->user_id = $request->user()->id;
    //             $transaction->invoice_id = $invoice->id;
    //             $transaction->amount_refunded = $invoice->amount_paid;
    //             $transaction->refund_status = 'refunded';
    //             $transaction->refund_date = now();
    //             $transaction->save();

    //             // Return a success message
    //             return redirect()->route('transactions.index')->with('success', 'Payment refunded and subscription canceled.');
    //         }

    //         return back()->withErrors(['error' => 'Refund could not be processed.']);
    //     } catch (Exception $e) {
    //         return back()->withErrors(['error' => 'Error processing refund: ' . $e->getMessage()]);
    //     }
    // }
    public function refund($invoiceId, Request $request)
    {
        $validated = $request->validate([
            'refund_reason' => 'required|string|max:255',
        ]);

        try {
            // Retrieve the invoice from Stripe
            $invoice = Invoice::retrieve($invoiceId);

            // Check if the invoice is already refunded or voided
            if ($invoice->status == 'uncollectible' || $invoice->status == 'void') {
                return back()->withErrors(['error' => 'This invoice cannot be refunded.']);
            }

            // Create a refund on Stripe
            $refund = StripeRefund::create([
                'charge' => $invoice->charge, // The charge ID from the invoice
                'reason' => $validated['refund_reason'], // Refund reason
            ]);
            // If refund successful, cancel the associated subscription on Stripe and update the database
            if ($refund) {
                // Cancel the subscription if needed
                $subscription = ModelSubscription::where('stripe_id', $invoice->subscription)->first();
                if ($subscription) {
                    $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_id);
                    $stripeSubscription->cancel();
                    $subscription->status = 'canceled';
                    $subscription->save();
                }

                // Save refund details locally
                $transaction = new Transaction();
                $transaction->user_id = $request->user()->id;
                $transaction->invoice_id = $invoice->id;
                $transaction->amount_refunded = $invoice->amount_paid;
                $transaction->refund_status = 'refunded';
                $transaction->refund_reason = $validated['refund_reason'];
                $transaction->refund_date = now();
                $transaction->save();

                return redirect()->route('transactions.index')->with('success', 'Payment refunded and subscription canceled.');
            }

            return back()->withErrors(['error' => 'Refund could not be processed.']);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error processing refund: ' . $e->getMessage()]);
        }
    }


    public function cancelRefund($invoiceId, Request $request)
    {
        try {
            // Find the invoice by its ID
            $invoice = Invoice::retrieve($invoiceId);

            // Check if the invoice has been refunded
            if ($invoice->status == 'paid' && $invoice->refund_status == 'refunded') {

                // Retrieve the refund using Stripe API
                $refund = Refund::retrieve($invoice->charge);

                // Cancel the refund (or reverse it if possible)
                $refund->cancel();

                // Update the local transaction and subscription status
                $transaction = Transaction::where('invoice_id', $invoice->id)->first();
                $transaction->refund_status = 'cancelled';
                $transaction->save();

                // Retrieve and reactivate the subscription
                $subscription = Subscription::where('stripe_id', $invoice->subscription)->first();
                $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_id);
                $stripeSubscription->reactivate(); // Assume reactivate method exists

                // Return a success message
                return back()->with('success', 'Refund canceled and subscription reactivated.');
            }

            return back()->withErrors(['error' => 'No refund to cancel for this invoice.']);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error canceling refund: ' . $e->getMessage()]);
        }
    }
}
