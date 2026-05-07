<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_behaviour_id',
        'category_id',
        'score',
    ];

    public function skillBehaviour()
    {
        return $this->belongsTo(SkillBehaviour::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
