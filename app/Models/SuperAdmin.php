<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'contactno',
        'office',
        'designation',
        'office_extension'
    ];
}
