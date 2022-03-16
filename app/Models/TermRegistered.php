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
