<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;


class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['permissions_set'];

    protected $hidden = ['id'];

    protected $casts = [
        'permissions_set'   =>  AsArrayObject::class,
    ];
}
