<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'name',
        'email',
        'department',
        'CGPA',
        'cr. hrs',
        'contact_no',
    ];

    public function studentAccount(){
        return $this->hasOne(StudentAccount::class, "registration_no", "registration_no");
    }

    public function studentdocuments(){
        return $this->hasOne(StudentDocs::class, "registration_no", "registration_no");
    }
}
