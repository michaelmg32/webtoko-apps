<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'paid_by',
        'amount',
        'payment_method',
        'payment_date',
        'payment_type',
        'remaining_amount',
        'dp_reference'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}