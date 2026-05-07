<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSkillBehaviour extends Model
{
    protected $fillable = ['skill_behaviour_id', 'student_id'];

    public function skillBehaviour()
    {
        return $this->belongsTo(SkillBehaviour::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scores()
    {
        return $this->hasMany(StudentCategoryScore::class, 'student_skill_behaviour_id');
    }
}
