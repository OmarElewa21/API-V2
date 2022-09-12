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
use Illuminate\Support\Facades\DB;

class Participant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesUuid, PowerJoins;
    
    const FILTER_COLUMNS = ['participants.name', 'participants.index', 'schools.name'];

    protected $fillable = [
        'index',
        'password',
        'name',
        'competition_id',
        'class',
        'grade',
        'organization_id',
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function tuition_centre()
    {
        return $this->belongsTo(School::class, 'tuition_centre_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class)->withTrashed();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            $index = $record->generateIndex($record->country, $record->school_id);
            $password = Str::random(14);
            $record->index = $index;
            $record->password = encrypt($password);
            $record->created_by = auth()->id();
        });

        static::created(function ($record){
            if($record->competition->mode !== 'Paper-Based'){
                $roundLevelIds = $record->competition->rounds()->joinRelationship('roundLevels')
                    ->select('round_levels.id')->where('round_levels.grades', 'LIKE', '%'.$record->grade.'%')->pluck('id');
                foreach($roundLevelIds as $roundLevelId){
                    $roundLevel = RoundLevel::whereId($roundLevelId)->with('defaultSession')->first();
                    DB::table('round_level_participant')->insert([
                        'participant_id'    => $record->id,
                        'round_level_id'    => $roundLevel->id,
                        'session_id'        => $roundLevel->defaultSession->id,
                        'assigned_by'       => auth()->id(),
                        'assigned_at'       => now()->toDateTimeString(),
                        'created_at'        => now()->toDateTimeString(),
                        'updated_at'        => now()->toDateTimeString()
                    ]);
                }
            }
        });

        static::updating(function ($record) {
            $record->updated_by = auth()->id();
        });

        static::deleted(function ($record) {
            $record->deleted_by = auth()->id();
            $record->save();
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

            if(isset($filterOptions['competition']) && !is_null($filterOptions['competition'])){
                $data->where('participants.competition_id', $filterOptions['competition']);
            }

            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $data->where('participants.status', $filterOptions['status']);
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

    public static function getFilterForFrontEnd($data){
        return collect([
            'filterOptions' => [
                    'school'           => $data->pluck('school', 'school_id')->unique(),
                    'country'          => $data->pluck('country', 'country_id')->unique(),
                    'grade'            => $data->pluck('grade')->unique(),
                    'competition'      => $data->pluck('competition', 'competition_id')->unique(),
                    'status'           => $data->pluck('status')->unique(),
                ]
            ]);
    }

    public static function allowedForRoute(self $participant)
    {
        switch (auth()->user()->role->name) {
            case 'country partner':
                return $participant->organization->country_partners()->whereId(auth()->id())->exists();
                break;
            case 'country partner assistant':
                return $participant->organization->country_partners()->whereId(auth()->user()->countryPartnerAssistant->country_partner_id)->exists();
                break;
            case 'school manager':
                return $participant->school_id === auth()->user()->schoolManager->school_id;
                break;
            case 'teacher':
                return $participant->school_id === auth()->user()->teacher->school_id;
                break;
            default:
                return true;
                break;
        }
    }
}
