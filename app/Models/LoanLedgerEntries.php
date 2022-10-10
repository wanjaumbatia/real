<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanLedgerEntries extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_model_id',
        'customer_id',
        'customer',
        'handler',
        'branch',
        'remarks',
        'debit',
        'credit',
        'amount'
    ];
}
