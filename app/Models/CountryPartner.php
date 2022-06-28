<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Scopes\UserScope;

class CountryPartner extends BaseModel
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'organization_id', 'deleted_at'];

    protected $hidden = ['user_id', 'organization_id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($country_partner) {
            $country_partner->user()->delete();
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function countryPartnerAssistants(){
        return $this->hasMany(CountryPartnerAssistant::class, 'country_partner_id', 'user_id');
    }
}
