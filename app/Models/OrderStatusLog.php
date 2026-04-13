<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'status_type',
        'old_status',
        'new_status',
        'changed_by'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}