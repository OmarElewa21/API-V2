<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;

class Competition extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'name',
        'identifier',
        'global_competition_start_date',
        'global_competition_end_date',
        're_run',
        'competition_format',
        'difficulty_group_id',
        'grades',
        'instructions',
        'status',
        'competition_reference',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'uuid'              => EfficientUuid::class,
        'grades'            => AsArrayObject::class
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'collection_tag', 'collection_id', 'tag_id');
    }
}
