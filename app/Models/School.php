<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;

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
        'status' 
    ];

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

    public static function applyFilter($filterOptions){
        if(isset($filterOptions['type']) && !is_null($filterOptions['type'])){
            switch ($filterOptions['type']) {
                case 'school':
                    $data = self::where('is_tuition_centre', 0);
                    break;
                case 'tuition_centre':
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
}
