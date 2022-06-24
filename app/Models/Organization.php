<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;

class Organization extends BaseModel
{
    use SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'person_in_charge_name',
        'address',
        'billing_address',
        'shipping_address',
        'img',
        'country_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    function __construct(){
        parent::__construct();
        $this->hidden[] = 'img';
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function country_partners(){
        return $this->hasManyThrough(User::class, CountryPartner::class, 'organization_id', 'id', 'id', 'user_id');
    }

    public static function applyFilter($filterOptions){
        if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
            $data = self::where('country_id', $filterOptions['country']);
        }
        return $data;
    }
}
