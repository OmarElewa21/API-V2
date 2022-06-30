<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;

class DomainsTags extends BaseModel
{
    use SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'is_tag',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function scopeDomains($query)
    {
        return $query->where('is_tag', 0);
    }

    public function scopeTags($query)
    {
        return $query->where('is_tag', 1);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
