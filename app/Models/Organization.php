<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Kirschbaum\PowerJoins\PowerJoins;

class Organization extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

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

    function __construct()
    {
        parent::__construct();
        $this->hidden[] = 'img';
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($organization) {
            $partners = User::whereRelation('countryPartner', 'organization_id', $organization->id)->get();
            foreach($partners as $partner){
                User::whereRelation('countryPartnerAssistant', 'country_partner_id', $partner->id)
                        ->orWhereRelation('schoolManager', 'country_partner_id', $partner->id)
                        ->orWhereRelation('teacher', 'country_partner_id', $partner->id)
                        ->update(['status'  => 'disabled']);
                $partner->update(['status'  => 'disabled']);
            }
        });
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function country_partners()
    {
        return $this->hasManyThrough(User::class, CountryPartner::class, 'organization_id', 'id', 'id', 'user_id');
    }

    public static function applyFilter($filterOptions)
    {
        if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
            $data = self::where('country_id', $filterOptions['country']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data)
    {
        $filter = $data->joinRelationshipUsingAlias('country', 'c')->select('c.id as id','c.name as name');
        return collect([
            'filterOptions' => [
                    'country'   => [$filter->pluck('name', 'id')->unique()],
                ]
            ]);
    }
}
