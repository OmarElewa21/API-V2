<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Scopes\UserScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryPartner extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'organization_id', 'country_id'];
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
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
}
