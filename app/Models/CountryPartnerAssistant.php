<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryPartnerAssistant extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = ['user_id', 'country_partner_id'];

    protected $hidden = ['user_id', 'country_partner_id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($countryPartnerAssistant) {
            $countryPartnerAssistant->user()->delete();
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function countryPartner(){
        return $this->belongsTo(CountryPartner::class, 'country_partner_id', 'user_id');
    }
}
