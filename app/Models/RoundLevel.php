<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Kirschbaum\PowerJoins\PowerJoins;

class RoundLevel extends Model
{
    use HasFactory, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'level',
        'round_id',
        'collection_id',
        'grades',
        'difficulty_and_points_identifier'
    ];

    protected $casts = [
        'uuid'          => EfficientUuid::class,
        'grades'        => AsArrayObject::class,
    ];
    
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
}
