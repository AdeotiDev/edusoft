<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCategoryScore extends Model
{
    protected $fillable = ['student_skill_behaviour_id', 'category_id', 'score'];

    public function studentEntry()
    {
        return $this->belongsTo(StudentSkillBehaviour::class, 'student_skill_behaviour_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
