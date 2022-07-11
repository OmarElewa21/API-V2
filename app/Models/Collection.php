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
use Illuminate\Support\Facades\DB;

class Collection extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'name',
        'identifier',
        'time_to_solve',
        'initial_points',
        'recommendations',
        'status',
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
                $q->status      = "active";
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

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'collection_tag', 'collection_id', 'tag_id');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class);
    }
}
