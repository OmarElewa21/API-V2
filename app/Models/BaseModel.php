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
    ];

    protected $casts = [
        'created_at'    => 'date:Y-m-d H:i:s',
        'updated_at'    => 'date:Y-m-d H:i:s',
        'deleted_at'    => 'date:Y-m-d H:i:s',
        'uuid'          => EfficientUuid::class,
    ];
    
    
    protected function createdBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' - ' . $attributes['created_at'] : $value
        );
    }

    protected function updatedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . $attributes['updated_by'] : $value
        );
    }

    protected function deletedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . $attributes['deleted_by'] : $value
        );
    }
}
