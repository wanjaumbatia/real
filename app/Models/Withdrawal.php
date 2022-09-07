<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'name',
        'amount',
        'handler',
        'description',
        'status',
        'branch',
        'confirmed_by',
        'document_number',
        'comission_amount',
        'commission_paid',
        'request_approval'
    ];
}
