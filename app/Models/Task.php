<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;

class Task extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'title',
        'identifier',
        'description',
        'img',
        'solution_working',
        'recommendations',
        'status',
        'answer_type',
        'answer_layout',
        'answer_structure',
        'answer_sorting',
        'answers_as_img',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'uuid'              => EfficientUuid::class,
        'recommendations'   => AsArrayObject::class,
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
            if(auth()->user()->hasRole(['super admin', 'admin'])){
                $q->status      = "approved";
                $q->approved_by = auth()->id();
                $q->approved_at = now();
            }
        });
    }

    protected function approvedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['approved_at'])) . ')' : $value
        );
    }

    public function domains()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags')->wherePivot('is_tag', 0);
    }

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags')->wherePivot('is_tag', 1);
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\Topic');
    }
    
    public function task_content()
    {
        return $this->hasOne(TaskContent::class);
    }

    public function task_answers()
    {
        return $this->hasMany(TaskAnswer::class);
    }
}
