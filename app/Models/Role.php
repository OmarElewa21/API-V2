<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Http\Scopes\RoleScope;
use Illuminate\Http\Request;

class Role extends BaseModel
{
    use SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'description',
        'permission_id',
        'is_fixed',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected $hidden = ['permission_id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($role) {
            $role->permission()->delete();
        });
    }

    public function users(){
        return $this->hasMany(User::class);
    }
    
    public function permission(){
        return $this->belongsTo(Permission::class)->withTrashed();
    }

    public static function applyFilter(Request $request, $data)
    {
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);
            if(isset($filterOptions['role']) && !is_null($filterOptions['role'])){
                $data = $data->where('name', $filterOptions['role']);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data = $data->where(function($query) use($search){
                $query->where('name', 'LIKE', '%'. $search. '%');
            });
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data)
    {
        return collect([
            'filterOptions' => [
                    'role'   => $data->pluck('name')->unique(),
                ]
        ]);
    }
}
