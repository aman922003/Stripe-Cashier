<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'amount_refunded',
        'refund_status',
        'refund_reason', // Add this column to handle the refund reason
        'refund_date',
    ];

    // Other relationships and methods...
}
