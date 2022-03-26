<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'purpose',
        'description',
        'start_date',
        'end_date',
        'coordinator_id',
    ];
}
