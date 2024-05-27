<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = ['amount', 'hours', 'price', 'target_address', 'source_address', 'resource', 'partial_fill', 'multisignature', 'total', 'txid', 'show_at'];

    protected $hidden = ['id', 'updated_at', 'deleted_at', 'show_at'];

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
}
