<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function checkout(Request $request, $name)
    {
        $user = $request->user();
        $plan = Plan::whereName($name)->firstOrFail();
        $planPrice = $plan->stripe_price_id;

        // Check for active subscription
        $activeSubscriptions = $user->subscriptions()->where('stripe_status', 'active')->get();

        if ($activeSubscriptions->isNotEmpty()) {
            foreach ($activeSubscriptions as $sub) {
                $stripeSub = \Stripe\Subscription::retrieve($sub->stripe_id);

                foreach ($stripeSub->items->data as $item) {
                    if ($item->price->id === $planPrice) {
                        return redirect()
                            ->route('subscriptions.index')
                            ->with('error', 'You are already subscribed to the ' . ucfirst($name) . ' plan.');
                    }
                }

                return redirect()
                    ->route('subscriptions.index')
                    ->with('error', 'You already have an active subscription. Please cancel it before subscribing to a new plan.');
            }
        }

        // AUTO APPLY COUPON
        $assignedCoupon = $user->coupons()->first();
        $options = [
            'success_url' => route('checkout.success'),
            'cancel_url' => route('dashboard'),
        ];

        if ($assignedCoupon && $assignedCoupon->promo_id) {
            $options['discounts'] = [
                ['promotion_code' => $assignedCoupon->promo_id],
            ];
        }

        return $user->newSubscription('default', $planPrice)
            ->checkout($options);
    }

    public function viewAssignedUsers($id)
    {
        $coupon = Coupon::with('users')->findOrFail($id);
        return view('coupons.assigned_users', compact('coupon'));
    }

    public function unassignUser($couponId, $userId)
    {
        $coupon = Coupon::findOrFail($couponId);
        $coupon->users()->detach($userId);

        return redirect()->back()->with('success', 'User unassigned from coupon.');
    }

    public function success()
    {
        return view('checkoutsuccess');
    }
}
