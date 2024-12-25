<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = [
        'school_name',
        'caption',
        'address',
        'contact',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'frontend_theme',
        'principal_name',
        'principal_signature',
    ];
    
    
    protected $casts = [
        'logo' => 'array',
        'favicon' => 'array',
    ];


}
