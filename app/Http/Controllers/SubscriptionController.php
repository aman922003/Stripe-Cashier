<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Product;


class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Stripe::setApiKey(env('STRIPE_SECRET'));
    }


    public function index(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->subscriptions;

        $stripeSubscriptions = [];

        foreach ($subscriptions as $sub) {
            $stripeSub = StripeSubscription::retrieve($sub->stripe_id, [
                'expand' => ['items.data.price'],
            ]);

            // Manually fetch and attach the product if needed
            foreach ($stripeSub->items->data as &$item) {
                if (is_string($item->price->product)) {
                    $product = Product::retrieve($item->price->product);
                    $item->price->product = $product;
                }
            }
            $stripeSubscriptions[] = $stripeSub;
        }
        return view('subscriptions.index', compact('subscriptions', 'stripeSubscriptions'));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $sub = $user->subscriptions()->first();

        $stripeSub = StripeSubscription::retrieve($sub->stripe_id, [
            'expand' => ['items.data.price.product'],
        ]);

        return view('subscriptions.show', [
            'sub' => $sub,
            'stripeSub' => $stripeSub,
        ]);
    }

    public function redirectToStripePortal(Request $request, $id)
    {
        $user = $request->user();
        $sub = $user->subscriptions()->findOrFail($id);

        $session = PortalSession::create([
            'customer'   => $user->stripe_id,
            'return_url' => route('subscriptions.show', $id),
        ]);

        return redirect($session->url);
    }

    public function cancelSubscription(Request $request, $id)
    {
        $user = $request->user();
        $sub = $user->subscriptions()->findOrFail($id);


        StripeSubscription::update($sub->stripe_id, [
            'cancel_at_period_end' => true,
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription will cancel at period end.');
    }

    // public function cancelSubscription(Request $request)
    // {
    //     $user = auth()->user();
    //     $sub = $user->subscription('default');

    //     if (! $sub) {
    //         return redirect()->route('subscriptions.index')->with('error', 'No active subscription found.');
    //     }
    //     // Check if subscription is already canceled or in grace period
    //     if ($sub->ended() || $sub->onGracePeriod()) {
    //         return redirect()->back()->with('error', 'Your subscription is already canceled.');
    //     }
    //     // Cancel subscription
    //     $sub->cancel();
    //     return redirect()->route('subscriptions.index')->with('success', 'Subscription will cancel at period end.');
    // }

    public function resumeSubscription(Request $request, $id)
    {
        $user = $request->user();
        $sub = $user->subscriptions()->findOrFail($id);

        if (! $sub->onGracePeriod()) {
            return redirect()->back()->with('error', 'This subscription cannot be renewed.');
        }

        $sub->resume();

        return redirect()->back()->with('success', 'Your subscription has been renewed successfully.');
    }



}

