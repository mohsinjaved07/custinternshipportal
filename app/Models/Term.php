<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;
    protected $fillable = [
        'term_name',
        'apply_for_internship',
        'acquisition_offer_letter',
        'acquisition_completion_certificate',
        'final_evaluation'
    ];
}
