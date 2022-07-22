<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;
    protected $fillable = [
        'term_name',
        'sem_name',
        'term_started_date',
        'apply_for_internship',
        'acquisition_offer_letter',
        'acquisition_completion_certificate',
        'final_evaluation',
        'internship_plan'
    ];
}
