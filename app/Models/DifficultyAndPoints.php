<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;

class DifficultyAndPoints extends BaseModel
{
    use GeneratesUuid, PowerJoins;

    protected $fillable = [
        'task_id',
        'difficulty_group_level_id',
        'identifier',
        'correct_points',
        'wrong_points',
        'blank_points',
        'min_points',
        'max_points',
        'created_by'
    ];

    protected $casts = [
        'uuid'              => EfficientUuid::class,
        'correct_points'    => AsArrayObject::class,
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function difficulty_group_level()
    {
        return $this->belongsTo(DifficultyGroupLevel::class);
    }

    public function round_level()
    {
        return $this->belongsTo(RoundLevel::class, 'identifier', 'identifier');
    }
}
