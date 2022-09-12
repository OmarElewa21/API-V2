<?php

namespace App\Models;

use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Kirschbaum\PowerJoins\PowerJoins;

class RoundLevel extends BaseModel
{
    use GeneratesUuid, PowerJoins;

    protected $fillable = [
        'level',
        'round_id',
        'collection_id',
        'grades',
        'difficulty_and_points_identifier',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'uuid'          => EfficientUuid::class,
        'grades'        => AsArrayObject::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });

        static::created(function ($record){
            if($record->round->competition->mode !== 'Paper-Based'){
                Session::create([
                    'name'           => $record->level,
                    'round_level_id' => $record->id,
                    'is_default'     => 1
                ]);
            }
        });
    }
    
    public function round()
    {
        return $this->belongsTo(Round::class)->withTrashed();
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function difficulty_and_points()
    {
        return $this->hasMany(DifficultyAndPoints::class, 'identifier', 'difficulty_and_points_identifier');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function defaultSession(){
        return $this->hasOne(Session::class)->where('is_default', 1);
    }

    public function participants(){
        return $this->belongsToMany(Participant::class, 'round_level_participant');
    }

    public static function getFilterForFrontEnd($data){
        return collect([
            'filterOptions' => [
                    'status'    => $data->get()->pluck('sessions')->flatten()->pluck('status')->unique()->values()
                ]
            ]);
    }
}
