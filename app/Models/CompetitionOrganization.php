<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;

class CompetitionOrganization extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'competition_id',
        'organization_id',
        'allow_session_edits_by_partners',
        'registration_open',
        'competition_dates',
        'status',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'uuid'                  => EfficientUuid::class,
        'competition_dates'     => AsArrayObject::class
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
