<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_id',
        'promo_id',
        'name',
        'code',
        'type',
        'amount_off',
        'percent_off',
        'currencies',
        'redeem_by',
        'max_redemptions',
        'generate_code',
    ];

    protected $casts = [
        'currencies' => 'array',
        'redeem_by' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
