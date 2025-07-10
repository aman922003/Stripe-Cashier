<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponInvite extends Model
{
    protected $fillable = ['coupon_id', 'email'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
