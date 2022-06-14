<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'username',
        'permission_by_role',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'uuid' => EfficientUuid::class,
    ];

    public function scopeAdmins($query)
    {
        return $query->whereRelation('role', 'name', 'admin');
    }

    public function scopeCountryPartners($query)
    {
        return $query->whereRelation('role', 'name', 'country partner');
    }

    public function role(){
        return $this->belongsTo(Role::class)->withTrashed();
    }

    public function hasRole($roles){
        if(gettype($roles) === 'string'){
            return Str::lower($this->role->name) === Str::lower($roles);
        }else if(gettype($roles) === 'array'){
            foreach($roles as $role){
                if(Str::lower($this->role->name) === Str::lower($role)){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public function hasOwnPermissionSet(){
        return !$this->permission_by_role;
    }

    public function countryPartner(){
        return $this->hasOne(CountryPartner::class);
    }

    public function teacher(){
        return $this->hasOne(Teacher::class);
    }

    public function CountryPartnerAssistant(){
        return $this->hasOne(CountryPartnerAssistant::class);
    }

    public function getUserPermissionSet(){
        return $this->belongsToMany(
            Permission::class, 'user_permissions',
            'user_id', 'permission_id');
    }

    public function getRolePermissionSet(){
        return $this->role->permission;
    }

    public function checkRouteEligibility($route_name){
        if($this->hasOwnPermissionSet() && $this->getUserPermissionSet()->exists()){
            $permission_set = $this->getUserPermissionSet()->first();
        }else{
            $permission_set = $this->getRolePermissionSet();
        }

        if(Arr::exists($permission_set, 'all') && $permission_set['all'] == true){
            return true;
        }
        return true;
        switch ($route_name) {
            case 'roles.index':
                return Arr::exists($permission_set, 'roles') && $permission_set['roles']['view'] == true;
                break;
            default:
                break;
        }
    }
}
