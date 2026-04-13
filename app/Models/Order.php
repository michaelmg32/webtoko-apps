<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_id',
        'customer_name',
        'customer_phone',
        'order_type',
        'created_by',
        'total_price',
        'discount_percentage',
        'discount_amount',
        'dp_amount',
        'dp_status',
        'payment_status',
        'print_status',
        'pickup_status',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }
}