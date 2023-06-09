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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use App\Http\Scopes\ExtendUser;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesUuid, PowerJoins;

    const FILTER_COLUMNS = ['name', 'email'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'username',
        'permission_by_role',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'country_id',
        'about',
        'img'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
        'laravel_through_key',
        'created_at',
        'updated_at',
        'deleted_at',
        'img',
        'about'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'uuid' => EfficientUuid::class,
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

    public function scopeAdmins($query)
    {
        return $query->whereRelation('role', 'name', 'admin');
    }

    public function scopeCountryPartners($query)
    {
        return $query->whereRelation('role', 'name', 'country partner')
                ->join('country_partners as cp', 'cp.user_id', '=', 'users.id')
                ->select('users.*', 'cp.country_id', 'cp.organization_id');
    }

    public function scopeCountryPartnersAssitants($query)
    {
        return $query->whereRelation('role', 'name', 'country partner assistant')
                ->join('country_partner_assistants as cpa', 'cpa.user_id', '=', 'users.id')
                ->select('users.*', 'cpa.country_id', 'cpa.country_partner_id');
    }

    public function scopeSchoolManagers($query)
    {
        return $query->whereRelation('role', 'name', 'school manager')
                ->join('school_managers as sm', 'sm.user_id', '=', 'users.id')
                ->select('users.*', 'sm.country_id', 'sm.country_partner_id', 'sm.school_id');
    }

    public function scopeTeachers($query)
    {
        return $query->whereRelation('role', 'name', 'teacher')
                ->join('teachers as t', 't.user_id', '=', 'users.id')
                ->select('users.*', 't.country_id', 't.country_partner_id', 't.school_id');
    }

    public function role(){
        return $this->belongsTo(Role::class)->withTrashed();
    }

    public function countryPartner(){
        return $this->hasOne(CountryPartner::class);
    }

    public function teacher(){
        return $this->hasOne(Teacher::class);
    }

    public function countryPartnerAssistant(){
        return $this->hasOne(CountryPartnerAssistant::class);
    }

    public function schoolManager(){
        return $this->hasOne(SchoolManager::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function relatedCountryPartner(){
        switch ($this->role->name) {
            case 'school manager':
                return $this->schoolManager->countryPartner();
                break;
            case 'teacher':
                return $this->teacher->countryPartner();
                break;
            case 'country partner assistant':
                return $this->countryPartnerAssistant->countryPartner();
                break; 
            default:
                # code...
                break;
        }
    }

    public function school(){
        switch ($this->role->name) {
            case 'school manager':
                return $this->schoolManager->school();
                break;
            case 'teacher':
                return $this->teacher->school();
                break; 
            default:
                # code...
                break;
        }
    }

    public function getSchoolAttribute(){
        switch ($this->role->name) {
            case 'school manager':
                return collect($this->schoolManager()->with('school')->first()->school)->only('name', 'uuid');
                break;
            case 'teacher':
                return collect($this->teacher()->with('school')->first()->school)->only('name', 'uuid');
                break; 
            default:
                return null;
                break;
        }
    }

    public function getParentAttribute(){
        switch ($this->role->name) {
            case 'country partner assistant':
                return collect($this->countryPartnerAssistant()->with('countryPartner')->first()->countryPartner)->only('name', 'uuid');
                break;
            case 'school manager':
                return collect($this->schoolManager()->with('countryPartner')->first()->countryPartner)->only('name', 'uuid');
                break;
            case 'teacher':
                return collect($this->teacher()->with('countryPartner')->first()->countryPartner)->only('name', 'uuid');
                break; 
            default:
                return null;
                break;
        }
    }

    public function personal_access(){
        return $this->hasOne(PersonalAccessToken::class, 'tokenable_id', 'id')
                ->where('tokenable_type', 'App\Models\User')->latest('last_used_at');
    }

    public function permissionSet(){
        return $this->belongsToMany(
            Permission::class, 'user_permissions',
            'user_id', 'permission_id');
    }

    public function getPermissionSetAttribute(){
        return $this->permissionSet()->first()->permissions_set;
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

    public function hasVariablePermissionSet(){
        return !$this->permission_by_role || !$this->role->is_fixed;
    }

    public function getUserPermissionSet(){
        if($this->permission_by_role){
            return $this->role->permission->permissions_set;
        }
        return $this->getPermissionSetAttribute();
    }

    public function checkRouteEligibility($route_name){
        $permission_set = $this->getUserPermissionSet();
        if(Arr::exists($permission_set, 'all') && $permission_set['all'] == true){
            return true;
        }
        switch ($route_name) {
            case 'roles.index':
                return Arr::exists($permission_set, 'roles') && $permission_set['roles']['view'] == true;
                break;
            default:
                break;
        }
    }

    public static function getFilterForFrontEnd($filter){
        return collect([
            'filterOptions' => [
                    'role'      => $filter->pluck('role')->unique()
                                        ->filter(function ($value, $key) {
                                            return !is_null($value);
                                        })->values(),
                    'country'   => $filter->pluck('country_name', 'country_id')->unique()
                                        ->filter(function ($value, $key) {
                                            return !is_null($value);
                                        }),
                    'status'    => $filter->pluck('status')->unique()->values()
                ]
            ]);
    }

    public static function applyFilter(Request $request, $data){   
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);

            if(isset($filterOptions['role']) && !is_null($filterOptions['role'])){
                $data->whereRelation('role', 'name', $filterOptions['role']);
            }
    
            if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
                $data->where('country_id', $filterOptions['country']);
            }
    
            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $data->where('status', $filterOptions['status']);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data->where(function($query)use($search){
                $query->where('users.name', 'LIKE', '%'. $search. '%');
                foreach(User::FILTER_COLUMNS as $column){
                    $query->orwhere('users.' . $column, 'LIKE', '%'. $search. '%');
                }
            });
        }

        return $data;
    }

    public function allowedForRoute(self $user, $userType="teacher")
    {
        switch ($userType) {
            case 'country_partner_assistant':
                return CountryPartnerAssistant::allowedForRoute($user);
                break;
            case 'school_manager':
                return SchoolManager::allowedForRoute($user);
                break;
            case 'teacher':
                return Teacher::allowedForRoute($user);
                break;
            case 'participant':
                return Participant::allowedForRoute($user);
                break;
            default:
                return true;
                break;
        }
    }
}
