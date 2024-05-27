<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'access_log';

    protected $fillable = ['ip_address', 'url', 'https', 'request_type', 'get_values', 'post_values', ];

}
