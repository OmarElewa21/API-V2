<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Http\Scopes\RoleScope;

class Role extends Model
{
    use HasFactory, SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'description',
        'permission_id',
        'created_by',
        'updated_by'
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

    protected static function booted()
    {
        static::addGlobalScope(new RoleScope);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
    
    public function permission(){
        return $this->belongsTo(Permission::class)->withTrashed();
    }
}
