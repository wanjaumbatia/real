<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortageLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_executive',
        'expected_amount',
        'give_amount',
        'short',
        'reference',
        'cleared',
        'office_admin',
        'description',
        'reported',
        'resolved',
        'branch',
        'remarks'
    ];
}
