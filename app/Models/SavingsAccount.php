<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id','plans_id','created_by','name','active', 'branch', 'handler','plan','customer','customer_number'
    ];
   
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
 
    public function plan(){
        return $this->belongsTo(Plans::class);
    }
}
