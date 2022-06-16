<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class Participant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesUuid;

    public $incrementing = false;

    protected $primaryKey = 'index';

    protected $fillable = [
        'index',
        'password',
        'name',
        'class',
        'grade',
        'user_id',
        'school_id',
        'country_id',
        'tuition_centre_id',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            $index = $participant->generateIndex(Country::find($participant->country_id), $participant->school_id);
            $password = Str::random(10);
            $participant->index = $index;
            $participant->password = Crypt::encryptString($password);
            $participant->created_by = auth()->id();
        });

        static::updating(function ($participant) {
            $participant->updated_by = auth()->id();
            $participant->password = Crypt::encryptString($participant->password);
        });
    }

    public function generateIndex(Country $country, $school_id=null)
    {
        switch (Str::length($country->dial)) {
            case 1:
                $dial = '00' . $country->dial;
                break;
            case 2:
                $dial = '0' . $country->dial;
                break;
            default:
                $dial = $country->dial;
                break;
        }

        $tuition_centre = is_null($school_id) ? '0' : (School::find($school_id)->is_tuition_centre ? '1' : '0'); 
        $identifier = $dial . Str::of(now()->year)->after('20') . $tuition_centre;
        $last_record = self::where('index', 'like', $identifier .'%')->orderBy('index', 'DESC')->first();
        if(is_null($last_record)){
            $index = $identifier . '000001';
        }else{
            $counter = Str::of($last_record->index)->substr(6, 12)->value();
            $counter = strval(intval($counter) + 1);
            $index = $identifier . str_repeat('0', 6 - Str::length($counter)) . $counter;
        }
        return $index;
    }
}
