<?php

namespace App\Http\Controllers;

use App\Models\RoundLevel;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\RoundLevelParticipant;
use App\Http\Requests\UpdateRoundLevelParticipantRequest;

class RoundLevelParticipants extends Controller
{
    const FILTER_COLUMNS = ['participants.name', 'countries.name', 'schools.name', 'sessions.name', 'competition_teams.name', 'round_level_participant.status'];

    /**
     * Display a listing of the resource.
     *
     * @param App\Models\RoundLevel $round_level
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(RoundLevel $round_level, Request $request)
    {
        $headerData = RoundLevel::where('round_levels.id', $round_level->id)->joinRelationship('round')->joinRelationship('round.competition')
                            ->select('round_levels.id', 'round_levels.level as level', 'rounds.index as round', 'competitions.name as competition',
                                    DB::raw('DATE_FORMAT(competitions.global_competition_start_date, "%Y/%m/%d") as start_date'),
                                    DB::raw('DATE_FORMAT(competitions.global_competition_end_date, "%Y/%m/%d") as end_date'),
                                    DB::raw("(SELECT COUNT(DISTINCT(participants.country_id)) AS countires_count FROM `participants`
                                            JOIN round_level_participant ON round_level_participant.participant_id = participants.id
                                            WHERE round_level_participant.round_level_id = {$round_level->id}
                                            ) AS countires_count")
                                    )
                            ->withCount('sessions', 'participants')->first();

        $data = Participant::distinct()->join('round_level_participant', 'round_level_participant.participant_id', 'participants.id')
                ->where('round_level_participant.round_level_id', $round_level->id)
                ->joinRelationship('country')->joinRelationship('school')
                ->join('users', 'round_level_participant.assigned_by', 'users.id')
                ->join('roles', 'users.role_id', 'roles.id')
                ->leftJoin('sessions', 'round_level_participant.session_id', 'sessions.id')
                ->leftJoin('competition_teams', 'round_level_participant.competition_team_id', 'competition_teams.id')
                ->select('participants.*', 'countries.name as country', 'schools.name as school',
                    DB::raw('DATE_FORMAT(round_level_participant.assigned_at, "%Y/%m/%d %H:%i") as assigned_at'),
                    'round_level_participant.status', 'users.name as assigned_by', 'roles.name as assigned_by_user_role',
                    'sessions.name as session', 'sessions.id as session_id', 'competition_teams.name as team', 'competition_teams.id as team_id')
                ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5);
        
        $this->applyFilter($data, $request);

        $filterOptions = $this->getFilterForFrontend($data);
        
        return $filterOptions->merge(
            collect(["headerData" => $headerData])->merge(collect($data)))
            ->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']);
    }


    /**
     * apply request filter to the query
     * 
     * @param Illuminate\Http\Request $request
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Database\Eloquent\Builder $query
     */
    private function applyFilter($query, Request $request)
    {
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);

            if(isset($filterOptions['school']) && !is_null($filterOptions['school'])){
                $query->where('participants.school_id', $filterOptions['school']);
            }

            if(isset($filterOptions['country']) && !is_null($filterOptions['country'])){
                $query->where('participants.country_id', $filterOptions['country']);
            }
    
            if(isset($filterOptions['grade']) && !is_null($filterOptions['grade'])){
                $query->where('participants.grade', $filterOptions['grade']);
            }

            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $query->where('round_level_participant.status', $filterOptions['status']);
            }

            if(isset($filterOptions['team']) && !is_null($filterOptions['team'])){
                $query->where('competition_teams.id', $filterOptions['team']);
            }

            if(isset($filterOptions['session']) && !is_null($filterOptions['session'])){
                $query->where('sessions.id', $filterOptions['sessions']);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($q)use($search){
                $q->where('participants.name', 'LIKE', '%'. $search. '%');
                foreach(self::FILTER_COLUMNS as $column){
                    $q->orwhere($column, 'LIKE', '%'. $search. '%');
                }
            });
        }
        return $query;
    }


    /**
     * get fitler options for front end
     * 
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Support\Collection
     */
    private function getFilterForFrontend($query)
    {
        return collect([
            'filterOptions' => [
            'school'           => $query->pluck('school', 'school_id')->unique(),
            'country'          => $query->pluck('country', 'country_id')->unique(),
            'grade'            => $query->pluck('grade')->unique(),
            'status'           => $query->pluck('status')->unique(),
            'team'             => $query->pluck('team', 'team_id')->filter(function ($value, $key) {return !is_null($value);})->unique(),
            'session'          => $query->pluck('session', 'session_id')->unique()
        ]]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoundLevel $round_level, UpdateRoundLevelParticipantRequest $request)
    {
        foreach($request->participants as $participant_id){
            $round_level_participant = RoundLevelParticipant::where('participant_id', $participant_id)
                            ->where('round_level_id', $round_level->id)->firstOrFail();
            switch ($request->mode) {
                case 'team':
                    $round_level_participant->update(["competition_team_id" => $request->team]);
                    break;
                case 'session':
                    $round_level_participant->update(["session_id" => $request->session]);
                    break;
                case 'status':
                    $round_level_participant->update(["status" => $request->status]);
                    break;
                default:
                    break;
            }
        }
        return $round_level_participant;
    }
}
