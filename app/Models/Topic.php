<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Dyrynda\Database\Casts\EfficientUuid;

class Topic extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'domain_id', 'updated_by', 'updated_at'];

    protected $casts = [
        'uuid'          => EfficientUuid::class,
    ];

    protected $hidden = ['domain_id', 'deleted_at'];

    protected function updatedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['updated_at'])) . ')' : $value
        );
    }

    protected function deletedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['deleted_at'])) . ')' : $value
        );
    }

    public function domain()
    {
        return $this->belongsTo(DomainsTags::class, 'domain_id');
    }
}
