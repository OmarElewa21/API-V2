<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Scopes\UserScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryPartner extends BaseModel
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'organization_id', 'country_id', 'deleted_at'];

    protected $hidden = ['user_id', 'organization_id'];

    protected static function booted()
    {
        static::addGlobalScope(new UserScope);
    }

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
