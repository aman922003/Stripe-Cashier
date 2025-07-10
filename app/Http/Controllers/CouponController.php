<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Coupon as StripeCoupon;
use Stripe\PromotionCode;
use App\Models\User;
use Stripe\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\CouponAssigned;
use App\Models\CouponInvite;
use App\Mail\CouponInviteMail;


class CouponController extends Controller
{
    public function __construct()
    {
        // Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('coupons.create');
    }

    public function store(Request $request)
    {
        if ($request->has('currencies') && is_string($request->currencies)) {
            $request->merge([
                'currencies' => array_filter(array_map('trim', explode(',', $request->currencies)))
            ]);
        }

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:percentage,fixed',
            'percent_off' => 'nullable|required_if:type,percentage|integer|min:1|max:100',
            'amount_off' => 'nullable|required_if:type,fixed|numeric|min:0.01',
            'currencies' => 'nullable|array',
            'duration' => 'required|in:once,forever,repeating',
            'duration_in_months' => 'nullable|required_if:duration,repeating|integer',
            'max_redemptions' => 'nullable|integer|min:1',
            'generate_code' => 'boolean',
        ]);

        // Build coupon data safely
        $data = [
            'name' => $request->name,
            'percent_off' => $request->type === 'percentage' ? $request->percent_off : null,
            'amount_off' => $request->type === 'fixed' ? intval($request->amount_off * 100) : null,
            'currency' => $request->type === 'fixed' ? 'usd' : null,
            'duration' => $request->duration,
            'max_redemptions' => $request->max_redemptions,
            'redeem_by' => $request->redeem_by ? strtotime($request->redeem_by) : null,
        ];

        if ($request->duration === 'repeating') {
            $data['duration_in_months'] = $request->duration_in_months;
        }

        $stripeCoupon = StripeCoupon::create($data);

        $promoCode = null;
        $promoId = null;

        if ($request->generate_code) {
            $promo = PromotionCode::create([
                'coupon' => $stripeCoupon->id,
                'code' => strtoupper(str()->random(10)),
            ]);
            $promoCode = $promo->code;
            $promoId = $promo->id;
        }

        $coupon = new Coupon($request->all());
        $coupon->stripe_id = $stripeCoupon->id;
        $coupon->promo_id = $promoId;
        $coupon->code = $promoCode ?? null;
        $coupon->save();

        return redirect()->route('coupons.index')->with('success', 'Coupon created on Stripe and locally.');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        if ($request->has('currencies') && is_string($request->currencies)) {
            $request->merge([
                'currencies' => array_filter(array_map('trim', explode(',', $request->currencies)))
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'percent_off' => 'nullable|required_if:type,percentage|integer|min:1|max:100',
            'amount_off' => 'nullable|required_if:type,fixed|numeric|min:0.01',
            'currencies' => 'nullable|array',
            'max_redemptions' => 'nullable|integer|min:1',
            'redeem_by' => 'nullable|date',
            'duration' => 'required|in:once,forever,repeating',
            'duration_in_months' => 'nullable|required_if:duration,repeating|integer',
        ]);

        // Delete old coupon on Stripe
        if ($coupon->stripe_id) {
            StripeCoupon::retrieve($coupon->stripe_id)->delete();
        }

        // Recreate coupon on Stripe
        $data = [
            'name' => $request->name,
            'percent_off' => $request->type === 'percentage' ? $request->percent_off : null,
            'amount_off' => $request->type === 'fixed' ? intval($request->amount_off * 100) : null,
            'currency' => $request->type === 'fixed' ? 'usd' : null,
            'duration' => $request->duration,
            'max_redemptions' => $request->max_redemptions,
            'redeem_by' => $request->redeem_by ? strtotime($request->redeem_by) : null,
        ];

        if ($request->duration === 'repeating') {
            $data['duration_in_months'] = $request->duration_in_months;
        }

        $stripeCoupon = StripeCoupon::create($data);

        $promoCode = null;
        $promoId = null;

        if ($coupon->generate_code) {
            $promo = PromotionCode::create([
                'coupon' => $stripeCoupon->id,
                'code' => strtoupper(str()->random(10)),
            ]);
            $promoCode = $promo->code;
            $promoId = $promo->id;
        }

        $coupon->update([
            'name' => $request->name,
            'type' => $request->type,
            'percent_off' => $request->type === 'percentage' ? $request->percent_off : null,
            'amount_off' => $request->type === 'fixed' ? $request->amount_off : null,
            'currencies' => $request->currencies,
            'duration' => $request->duration,
            'duration_in_months' => $request->duration === 'repeating' ? $request->duration_in_months : null,
            'max_redemptions' => $request->max_redemptions,
            'redeem_by' => $request->redeem_by,
            'stripe_id' => $stripeCoupon->id,
            'promo_id' => $promoId,
            'code' => $promoCode,
        ]);

        return redirect()->route('coupons.index')->with('success', 'Coupon updated on Stripe and locally.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);

        if ($coupon->stripe_id) {
            StripeCoupon::retrieve($coupon->stripe_id)->delete();
        }

        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Coupon deleted on Stripe and locally.');
    }

    public function assignForm(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $users = User::query();

        if ($request->has('search')) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $users->latest()->get();

        return view('coupons.assign', compact('coupon', 'users'));
    }

    public function assignToUsers(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->user_ids as $userId) {
            $user = User::findOrFail($userId);
            $coupon->users()->syncWithoutDetaching([$userId]);
                if ($coupon->promo_id && $user->stripe_id) {
                $stripeCustomer = Customer::retrieve($user->stripe_id);

                // Apply promotion code to customer
                Customer::update($stripeCustomer->id, [
                    'promotion_code' => $coupon->promo_id,
                ]);
            }
        }
        Mail::to($user->email)->send(new CouponAssigned($coupon, $user));
        return redirect()->route('coupons.assigned.users.all')->with('success', 'Coupon assigned successfully.');
    }

    public function viewAllAssignedUsers()
    {
        $users = User::whereHas('coupons')->with('coupons')->get();
        return view('coupons.assigned_users', compact('users'));
    }

   public function unassignUser($couponId, $userId)
    {
        $coupon = Coupon::findOrFail($couponId);
        $user = User::findOrFail($userId);

        // 1. Detach coupon from pivot table
        $coupon->users()->detach($userId);

        // 2. Remove Stripe-level discounts
        if ($coupon->promo_id && $user->stripe_id) {
            try {
                // Retrieve Stripe Customer
                $stripeCustomer = \Stripe\Customer::retrieve($user->stripe_id);

                // Remove any customer-level discounts
                if (isset($stripeCustomer->discount)) {
                    $stripeCustomer->deleteDiscount();
                }

                // Fetch all active subscriptions for the customer
                $subscriptions = \Stripe\Subscription::all([
                    'customer' => $user->stripe_id,
                    'status' => 'active',
                ]);

                foreach ($subscriptions->data as $sub) {
                    if (isset($sub->discount)) {
                        // Retrieve and remove discount from subscription
                        $subscription = \Stripe\Subscription::retrieve($sub->id);
                        $subscription->deleteDiscount();
                    }
                }

            } catch (\Exception $e) {
                return back()->with('error', 'Error unassigning coupon: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Coupon unassigned successfully from user and Stripe.');
    }

// public function sendToFriend(Request $request, $id)
// {
//     $coupon = Coupon::findOrFail($id);

//     $request->validate([
//         'friend_email' => 'required|email',
//     ]);

//     $friendEmail = $request->friend_email;

//     // Try to find existing user
//     $user = User::where('email', $friendEmail)->first();

//     if (!$user) {
//         // Create a new placeholder user
//         $user = User::create([
//             'name' => 'Invited User',
//             'email' => $friendEmail,
//             'password' => bcrypt(Str::random(12)), // Dummy password
//         ]);
//     }

//     // If the user doesn't yet have a Stripe ID, create one
//     if (!$user->stripe_id) {
//         $stripeCustomer = Customer::create([
//             'email' => $user->email,
//             'name' => $user->name,
//         ]);
//         $user->stripe_id = $stripeCustomer->id;
//         $user->save();
//     }

//     // Assign coupon in pivot table
//     $coupon->users()->syncWithoutDetaching([$user->id]);

//     // Assign promo code in Stripe
//     if ($coupon->promo_id) {
//         Customer::update($user->stripe_id, [
//             'promotion_code' => $coupon->promo_id,
//         ]);
//     }

//     // Send the assigned coupon email
//     Mail::to($user->email)->send(new CouponAssigned($coupon, $user));

//     return back()->with('success', 'Coupon sent and assigned to ' . $user->email);
// }


    public function sendToFriend(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $request->validate([
            'friend_email' => 'required|email',
        ]);

        // Store invite
        CouponInvite::create([
            'coupon_id' => $coupon->id,
            'email' => $request->friend_email,
        ]);

        // Build the registration URL
        $registrationUrl = route('register');

        // Send mail
        Mail::to($request->friend_email)->send(new CouponInviteMail($coupon, $registrationUrl));

        return back()->with('success', 'Invitation sent!');
    }
}
