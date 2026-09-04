<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public const STATUS_PENDING = 'pending';

public const STATUS_CONFIRMED = 'confirmed';

public const STATUS_PROCESSING = 'processing';

public const STATUS_SHIPPING = 'shipping';

public const STATUS_COMPLETED = 'completed';

public const STATUS_CANCELLED = 'cancelled';

public const PAYMENT_UNPAID = 'unpaid';

public const PAYMENT_PAID = 'paid';

public const PAYMENT_REFUNDED = 'refunded';
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'receiver_name',
        'receiver_phone',
        'province_name',
        'district_name',
        'ward_name',
        'address_line',
        'note',
        'ordered_at',
        'cancel_reason',
'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function histories()
{
    return $this->hasMany(
        OrderStatusHistory::class
    )->latest('id');
}
}