<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Topic extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'domain_id', 'updated_by', 'updated_at'];

    protected function updatedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' - ' . $attributes['updated_at'] : $value
        );
    }

    public function domains()
    {
        return $this->belongsTo(DomainsTags::class);
    }
}
