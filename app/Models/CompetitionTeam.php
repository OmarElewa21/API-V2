<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CompetitionTeam extends BaseModel
{
    use SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'competition_id',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function competition()
    {
        return $this->blongsTo(Competition::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
