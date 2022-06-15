<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'login_id',
        'password',
        'one_time_auth'
    ];
}
