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
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Contracts\Encryption\DecryptException;

class Participant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesUuid, PowerJoins;
    
    const STATUS = [
        "Active"            => 1,
        "Absent"            => 2,
        "Pending Marking"   => 3,
        "Computed"          => 4
    ];
    const FILTER_COLUMNS = ['participants.name', 'participants.index', 'schools.name'];

    public $incrementing = false;

    protected $primaryKey = 'index';

    protected $fillable = [
        'index',
        'password',
        'name',
        'class',
        'grade',
        'country_partner_id',
        'school_id',
        'country_id',
        'tuition_centre_id',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'approved_at'
    ];

    protected $casts = [
        'uuid'      => EfficientUuid::class,
    ];

    protected function createdBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['created_at'])) . ')' : $value
        );
    }

    protected function updatedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['updated_at'])) . ')' : $value
        );
    }

    protected function deletedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['deleted_at'])) . ')' : $value
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => array_search($value, self::STATUS) ? array_search($value, self::STATUS) : $value
        );
    }

    protected function getPasswordAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            $password = Str::random(14);
            $this->password = encrypt($password);
            $this->save();
            return $password;
        }
    }

    public function countryPartner()
    {
        return $this->belongsTo(User::class, 'country_partner_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            $index = $participant->generateIndex($participant->country, $participant->school_id);
            $password = Str::random(14);
            $participant->index = $index;
            $participant->password = encrypt($password);
            $participant->created_by = auth()->id();
        });

        static::updating(function ($participant) {
            $participant->updated_by = auth()->id();
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

    public static function applyFilter(Request $request, $data)
    {
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);

            if(isset($filterOptions['school']) && !is_null($filterOptions['school'])){
                $data->where('participants.school_id', $filterOptions['school']);
            }

            if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
                $data->where('participants.country_id', $filterOptions['country']);
            }
    
            if(isset($filterOptions['grade']) && !is_null($filterOptions['grade'])){
                $data->where('participants.grade', $filterOptions['grade']);
            }
    
            if(isset($filterOptions['status']) && !is_null($filterOptions['status']) && Arr::exists(self::STATUS, $filterOptions['status'])){
                $data->where('participants.status', self::STATUS[$filterOptions['status']]);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data->where(function($query)use($search){
                $query->where('participants.name', 'LIKE', '%'. $search. '%');
                foreach(self::FILTER_COLUMNS as $column){
                    $query->orwhere($column, 'LIKE', '%'. $search. '%');
                }
            });
        }
        return $data;
    }

    public static function getFilterForFrontEnd($filter){
        $statuses = $filter->pluck('status')->unique()->toArray();
        return collect([
            'filterOptions' => [
                    'school'           => $filter->pluck('school', 'school_id')->unique(),
                    'country'          => $filter->pluck('country', 'country_id')->unique(),
                    'grade'            => $filter->pluck('grade')->unique(),
                    'status'           => array_keys(Arr::where(self::STATUS, function ($value, $key) use($statuses){
                                            return in_array($value, $statuses);
                                        }))
                ]
            ]);
    }
}
