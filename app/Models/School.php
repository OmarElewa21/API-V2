<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class School extends BaseModel
{
    const FILTER_COLUMNS = ['name', 'address', 'phone', 'email', 'postal_code', 'province'];

    use SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'email',
        'province',
        'address',
        'postal_code',
        'phone',
        'country_id',
        'is_tuition_centre',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'status',
        'approved_by',
        'approved_at'
    ];

    protected $appends = ['teachers'];

    protected function approvedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['approved_at'])) . ')' : $value
        );
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function teachers(){
        return $this->hasMany(Teacher::class);
    }

    public function rejections(){
        return $this->morphMany(Rejection::class, 'relation');
    }

    public function rejection(){
        return $this->morphOne(Rejection::class, 'relation')->ofMany('count', 'max');
    }

    public function getTeachersAttribute()
    {
        return $this->teachers()->joinRelationship('user')->get()->pluck('user')->map->only(['uuid','name']);
    }

    public function scopeGetRelatedUserSchoolsBasedOnCountry(){
        return $this->where('country_id', auth()->user()->getRelatedUser()->country_id);
    }

    public static function applyFilter(Request $request, $data){
        if($request->has('filterOptions')){
            $request->validate([
                'filterOptions'                 => 'array',
                'filterOptions.type'            => ['string', Rule::in(['school', 'tuition centre'])],
                'filterOptions.country'         => 'exists:countries,id',
                'filterOptions.status'          => ['string', Rule::in(['pending', 'approved', 'rejected', 'deleted'])]
            ]);
            $filterOptions = $request->filterOptions;

            if(isset($filterOptions['type']) && !is_null($filterOptions['type'])){
                switch ($filterOptions['type']) {
                    case 'school':
                        $data = $data->where('is_tuition_centre', 0);
                        break;
                    case 'tuition centre':
                        $data = $data->where('is_tuition_centre', 1);
                        break;
                    default:
                        $data = $data->whereIn('is_tuition_centre', [1,0]);               
                        break;
                }
            }
            if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
                $data = $data->where('country_id', $filterOptions['country']);
            }
            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $data = $data->where('status', $filterOptions['status']);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data = $data->where(function($query)use($search){
                $query->where('schools.name', 'LIKE', '%'. $search. '%');
                foreach(School::FILTER_COLUMNS as $column){
                    $query->orwhere('schools.'. $column, 'LIKE', '%'. $search. '%');
                }
            });
        }
        return $data;
    }

    public static function getFilterForFrontEnd($schools){
        $filter = $schools->Join('countries', 'schools.country_id', '=', 'countries.id')
                        ->select('schools.status', 'schools.country_id', 'countries.name');
        
        return collect([
            'filterOptions' => [
                    'type'      => [$filter->selectRaw("CASE WHEN is_tuition_centre=1 THEN 'tuition centre' ELSE 'school' END AS type")
                                        ->pluck('type')->unique()->values()],
                    'country'   => [$filter->distinct('country_id')->pluck('name', 'country_id')],
                    'status'    => $filter->pluck('status')->unique()->values(),
                ]
            ]);
    }
}
