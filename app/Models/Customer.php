<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','address','gender','town','phone','posted','no','email',
        'dob', 'handler','address','branch','bank','bankacc','business', 'created_by'
    ];

    public function bankaccounts(){
        return $this->hasMany(BankAccounts::class);
    }
}
