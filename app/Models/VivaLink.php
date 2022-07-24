<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VivaLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'link',
        'date'
    ];
}
