<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealInvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'is_customer',
        'customer_id',
        'customer_name',
        'phone',
        'address',
        'handler',
        'amount',
        'duration',
        'percentage',
        'start_date',
        'exit_date',
    ];
}
