<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Scopes\UserScope;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'country_partner_id', 'country_id', 'school_id', 'deleted_at'];

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

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function coutry_partner(){
        return $this->belongsTo(CountryPartner::class, 'user_id', 'country_partner_id');
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }
}
