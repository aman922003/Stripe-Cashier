<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Stripe\Customer;
use App\Models\Coupon;
use App\Models\CouponInvite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */

    public function create(Request $request)
    {
        return view('auth.register');
    }


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
     public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $invite = CouponInvite::where('email', $user->email)->first();

        if ($invite) {
            $coupon = $invite->coupon;

            if ($coupon) {
                $user->coupons()->attach($coupon->id);

                if ($coupon->promo_id) {
                    if (!$user->stripe_id) {
                        $stripeCustomer = Customer::create([
                            'email' => $user->email,
                            'name' => $user->name,
                        ]);
                        $user->stripe_id = $stripeCustomer->id;
                        $user->save();
                    }

                    Customer::update($user->stripe_id, [
                        'promotion_code' => $coupon->promo_id,
                    ]);
                }
            }

            $invite->delete();
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created and your coupon has been applied!');
    }

}
