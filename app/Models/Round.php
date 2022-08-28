<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;

class Round extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'competition_id',
        'index',
        'label',
        'configurations',
        'contribute_to_individual_score',
        'one_account_answer_tasks',
        'tasks_assigned_by_leader',
        'free_for_all',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function round_level()
    {
        return $this->hasMany(RoundLevel::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class)->withTrashed();
    }
}
