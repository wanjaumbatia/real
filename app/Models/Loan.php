<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'name',
        'application_date',
        'amount',
        'interest_percentage',
        'duration',
        'current_savings',
        'handler',
        'status',
        'remarks',
        'posted',
        'date_posted',
        'customer_id'
    ];
}
