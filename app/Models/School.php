<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;

class School extends BaseModel
{
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

    function __construct(){
        parent::__construct();
        $this->hidden[] = 'approved_at';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function country_data(){
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

    public static function applyFilter($filterOptions){
        if(isset($filterOptions['type']) && !is_null($filterOptions['type'])){
            switch ($filterOptions['type']) {
                case 'school':
                    $data = self::where('is_tuition_centre', 0);
                    break;
                case 'tuition centre':
                    $data = self::where('is_tuition_centre', 1);
                    break;
                default:
                    $data = self::whereIn('is_tuition_centre', [1,0]);               
                    break;
            }
        }else{
            $data = new School;
        }
        if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
            $data = $data->where('country_id', $filterOptions['country']);
        }
        if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd(){
        $filter = School::withTrashed()->Join('countries', 'schools.country_id', '=', 'countries.id')
                    ->select('schools.status', 'schools.country_id', 'countries.name');
        return collect([
            'filterOptions' => [
                    'type'      => ['school', 'tuition centre'],
                    'country'   => [
                        $filter->distinct('country_id')->pluck('name', 'country_id'),
                    ],
                    'status'    => $filter->pluck('status')->unique()->values(),
                ]
            ]);
    }

    protected function approvedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' - ' . $attributes['approved_at'] : $value
        );
    }
}
