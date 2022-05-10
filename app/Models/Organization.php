<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $fillable = [
        'organization_ntn_no',
        'organization_name',
        'organization_email',
        'organization_contact',
        'organization_address',
        'organization_website'
    ];
}
