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

    protected $fillable = ['amount', 'days', 'price', 'address', 'source'];

    protected $hidden = ['id', 'updated_at', 'deleted_at'];

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
