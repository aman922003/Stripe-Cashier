<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Invoice;

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
        return $stripeInvoices;

        return view('transactions.index', [
            'invoices' => collect($stripeInvoices->data),
        ]);
    }
}
