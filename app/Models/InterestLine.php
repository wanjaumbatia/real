<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id', 'customer_name', 'plan','customer_id', 'savings_account_id', 'amount', 'status', 'approved_by', 'branch', 'handler', 'reference'
    ];
}
