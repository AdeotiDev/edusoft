<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillBehaviour extends Model
{
    protected $fillable = ['result_root_id', 'class_id'];

    public function resultRoot()
    {
        return $this->belongsTo(ResultRoot::class, 'result_root_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students() // child rows for this upload
    {
        return $this->hasMany(StudentSkillBehaviour::class);
    }
}
