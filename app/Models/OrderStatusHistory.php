<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    public const CHANGED_BY_ADMIN = 'admin';

    public const CHANGED_BY_CUSTOMER = 'customer';

    public const CHANGED_BY_SYSTEM = 'system';

    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'note',
        'changed_by',
        'changed_by_type',
    ];

    protected function casts(): array
    {
        return [
            'changed_by' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changer()
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}