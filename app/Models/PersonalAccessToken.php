<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $hidden = ['tokenable_id'];

    public function tokenable()
    {
        return $this->morphTo();
    }

    protected function lastLogin(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? date('d/m/Y H:i', strtotime($value)) : $value
        );
    }
}
