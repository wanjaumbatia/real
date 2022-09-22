<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'savings_account_id',
        'plan',
        'customer_id',
        'customer_name',
        'transaction_type',
        'status',
        'remarks',
        'debit',
        'credit',
        'reconciled',
        'amount',
        'requires_approval',
        'approved',
        'posted',
        'created_by',
        'cps',
        'first_approver',
        'second_approver',
        'sent_cps',
        'batch_number',
        'branch',
        'reference',
    ];

    public function savings_account(){
        return $this->hasOne(SavingsAccount::class);
    }

    public function commision(){
        return $this->hasOne(CommissionLines::class);
    }
}
