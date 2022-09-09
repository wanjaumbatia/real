<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number','no','name','amount','handler','branch','posted',
        'description','status','confirmed_by','document_number','purpose'
    ];

}
