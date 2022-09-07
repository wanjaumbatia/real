<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'no', 'name', 'amount', 'handler', 'description', 'status', 'confirmed_by', 'document_number', 'branch'
    ];
}
