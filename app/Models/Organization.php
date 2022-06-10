<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;


class Organization extends Model
{
    use HasFactory, SoftDeletes, GeneratesUuid;

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'person_in_charge_name',
        'address',
        'billing_address',
        'shipping_address',
        'img',
        'country_id'
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function country_partners(){
        return $this->hasMany(CountryPartner::class);
    }
}
