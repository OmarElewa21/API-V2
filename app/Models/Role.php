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
        'permission_id',
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($role) {
            $role->permission()->delete();
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permission(){
        return $this->belongsTo(Permission::class)->withTrashed();
    }
}
