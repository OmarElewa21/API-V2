<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolManager extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = ['user_id', 'country_partner_id', 'school_id'];

    protected $hidden = ['user_id', 'country_partner_id', 'school_id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($schoolManager) {
            $schoolManager->user()->delete();
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function countryPartner(){
        return $this->belongsTo(User::class, 'country_partner_id');
    }

    public static function allowedForRoute(User $user)
    {
        switch (auth()->user()->role->name) {
            case 'country partner':
                return $user->schoolManager->countryPartner->id === auth()->id();
                break;
            case 'country partner assistant':
                return $user->schoolManager->countryPartner->id === auth()->user()->countryPartnerAssistant->countryPartner->id;
                break;
            default:
                return true;
                break;
        }
    }
}
