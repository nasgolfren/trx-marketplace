<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = ['amount', 'hours', 'price', 'target_address', 'source_address', 'resource', 'partial_fill', 'multisignature', 'total', 'txid', 'show_at'];

    protected $hidden = ['id', 'updated_at', 'deleted_at', 'show_at'];

    protected $appends = [
        'filled_amount',
    ];

    /**
     * Boot method to set default values.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $order) {
            $order->unique_id = Str::uuid();
        });
    }

    public function getFilledAmountAttribute(): int
    {
        return $this->sellOrders()->sum('amount');
    }

    public function sellOrders(): HasMany
    {
        return $this->hasMany(OrderSell::class);
    }
}
