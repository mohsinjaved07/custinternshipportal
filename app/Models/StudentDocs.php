<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocs extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'recommendation_letter',
    ];
}
