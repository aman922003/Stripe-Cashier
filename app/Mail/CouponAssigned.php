<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CouponAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $coupon;
    public $user;

    public function __construct(Coupon $coupon, User $user)
    {
        $this->coupon = $coupon;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('You have been assigned a new coupon!')
                    ->markdown('emails.coupons.assigned');
    }
}
