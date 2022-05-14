<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermRegistered extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'term_name',
        'coordinator_id',
        'organization_ntn_no',
        'organization_name',
        'organization_email',
        'organization_contact',
        'organization_address',
        'organization_website',
        'supervisor_name',
        'supervisor_email',
        'supervisor_designation',
        'supervisor_contact',
        'offer_letter',
        'offer_letter_uploaded_date',
        'internship_report',
        'internship_report_uploaded_date',
        'internship_completion_certificate',
        'internship_completion_certificate_uploaded_date',
        'days_remaining'
    ];
    
    public function students(){
        return $this->hasOne(Student::class, "registration_no", "registration_no");
    }

    public function terms(){
        return $this->hasOne(Term::class, "term_name", "term_name");
    }

    public function coordinators(){
        return $this->hasOne(Coordinator::class, "id", "coordinator_id");
    }
}
