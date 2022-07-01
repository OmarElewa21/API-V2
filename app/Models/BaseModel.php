<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Dyrynda\Database\Casts\EfficientUuid;

class BaseModel extends Model
{
    use HasFactory;

    protected $hidden = [
        'id', 
        'created_at',
        'updated_at',
        'deleted_at',
        'approved_at'
    ];

    protected $casts = [
        'uuid'          => EfficientUuid::class,
    ];
    
    protected function createdBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['created_at'])) . ')' : $value
        );
    }

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
}
