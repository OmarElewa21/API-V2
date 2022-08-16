<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Award extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'competition_id',
        'round_index',
        'by_position',
        'use_grade_to_assign_points',
        'min_points',
        'use_min_points_for_all',
        'default_award',
        'default_points',
        'is_overall',
        'labels',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'labels'  => AsArrayObject::class
    ];

    public function competition()
    {
        return $this->belongTo(Competition::class);
    }

    public function award_labels()
    {
        return $this->hasMany(AwardLabel::class);
    }
}
