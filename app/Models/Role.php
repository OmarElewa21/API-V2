<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class Role extends Model
{
    use HasFactory, SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'description',
        'privileges',
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions(){
        return $this->belongsTo(Permission::class);
    }
}
