<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name', 'bank_account', 'bank_branch', 'created_by', 'customer_id'
    ];
    
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
