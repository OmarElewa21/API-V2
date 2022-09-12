<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoundLevelParticipant extends Model
{
    use HasFactory;

    const STATUSSES = ['In Progress', 'Active', 'Completed', 'Inactive', 'Banned'];

    protected $table = 'round_level_participant';

    protected $primaryKey = 'participant_id';

    protected $fillable = [
        'session_id',
        'competition_team_id',
        'status'
    ];
}
