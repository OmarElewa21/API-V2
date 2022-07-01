<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DifficultyGroupLevel extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'difficulty_group_id', 'correct_points', 'wrong_points', 'blank_points'];

    protected $hidden = ['id', 'difficulty_group_id', 'deleted_at'];

    public function difficulty_group()
    {
        return $this->belongsTo(DifficultyGroup::class);
    }
}
