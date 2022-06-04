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
        'supervisor_department',
        'offer_letter',
        'offer_letter_uploaded_date',
        'offer_letter_status',
        'start_date',
        'end_date',
        'internship_report',
        'internship_completion_certificate',
        'internship_evaluation_performa',
        'document_uploaded_date',
        'document_status'

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

    public function internconfirm(){
        return $this->hasOne(InternConfirm::class, "registration_no", "registration_no");
    }
}
