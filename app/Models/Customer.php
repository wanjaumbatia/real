<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','address','gender','town','phone','posted','no',
        'dob', 'handler','address','branch','bank','bankacc','business'
    ];
}
