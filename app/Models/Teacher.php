<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Teacher extends Model
{
    use HasFactory, PowerJoins;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = ['user_id', 'country_partner_id', 'school_id'];

    protected $hidden = ['user_id', 'country_partner_id', 'school_id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($teacher) {
            $teacher->user()->delete();
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function countryPartner(){
        return $this->belongsTo(CountryPartner::class, 'country_partner_id', 'user_id');
    }
}
