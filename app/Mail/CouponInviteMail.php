<?php

namespace App\Mail;

use App\Models\Coupon;
use Illuminate\Mail\Mailable;

class CouponInviteMail extends Mailable
{
    public $coupon;
    public $registrationUrl;

    public function __construct(Coupon $coupon, $registrationUrl)
    {
        $this->coupon = $coupon;
        $this->registrationUrl = $registrationUrl;
    }

    public function build()
    {
        return $this->subject('Your Coupon Invitation')
                    ->markdown('emails.coupons.coupon_invite');
    }
}
