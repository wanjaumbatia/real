<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionLines extends Model
{
    use HasFactory;

    protected $fillable = [
        'handler', 'amount', 'description', 'batch_number', 'payment_id', 'disbursed', 'approved', 'approved_by', 'transaction_type', 'branch'
    ];
}
