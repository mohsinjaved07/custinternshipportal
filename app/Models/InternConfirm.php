<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternConfirm extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_no',
        'link',
        'expire_date',
        'status',
    ];

    public function students(){
        return $this->hasOne(Student::class, 'registration_no', 'registration_no');
    }
}
