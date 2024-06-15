<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderSell extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders_sell';

    protected $fillable = ['order_id', 'payout_target_address', 'amount', 'reward', 'total_reward', 'txid', 'reward_txid'];

    protected $hidden = ['id', 'updated_at', 'deleted_at', 'show_at'];


    /**
     * Boot method to set default values.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (OrderSell $orderSell) {
            $orderSell->unique_id = Str::uuid();
        });
    }
}
