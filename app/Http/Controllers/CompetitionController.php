<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionOrganization;
use App\Models\Round;
use App\Models\RoundLevel;
use App\Models\Award;
use App\Models\AwardLabel;
use App\Http\Requests\competition\StoreCompetitionRequest;
use App\Http\Requests\competition\UpdatecompetitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Competition::with([
                'organizations' => function($query){
                    $query->joinRelationship('organization')
                        ->select('competition_organizations.competition_id', 'competition_organizations.organization_id', 'organizations.name');
                },
                'tags:id,name',
                'rounds' => function($query){
                    $query->joinRelationship('round_level')->joinRelationship('round_level.collection')
                        ->select('rounds.id', 'rounds.competition_id', 'round_levels.level', 'round_levels.grades', 'collections.name');
                },
                'round_awards:id,competition_id,labels'
            ])->withCount('organizations', 'tags', 'rounds');
        
        Competition::applyFilter($request, $data);

        $filterOptions = Competition::getFilterForFrontEnd($data);
        
        return response($filterOptions->merge($data
                ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']), 200); 
    }


    /***************************************** Storing *****************************************/
    /**
     * store organizations for competition
     * @param \App\Models\Competition  $competition
     * @param array $data
     */
    private function storeOrganizations(Competition $competition, $data)
    {
        foreach($data['organizations'] as $organization){
            $organization['competition_dates'] = 
                Arr::map(explode('-', $organization['competition_dates']), function ($value, $key) {
                    return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
                });
            $organization['competition_id'] = $competition->id;
            $organization['registration_open'] = Carbon::createFromFormat('m/d/Y', $organization['registration_open'])->format('Y-m-d');
            
            CompetitionOrganization::create($organization);

            if(Arr::has($organization, 'languages_to_translate')){
                foreach($organization['languages_to_translate'] as $lang_id){
                    DB::table('competition_organization_languages')->insert([
                        'competition_id'            => $competition->id,
                        'organization_id'           => $organization['organization_id'],
                        'language_id'               => $lang_id,
                        'to_view'                   => false
                    ]);
                }
            }
            if(Arr::has($organization, 'languages_to_view')){
                foreach($organization['languages_to_view'] as $lang_id){
                    DB::table('competition_organization_languages')->insert([
                        'competition_id'            => $competition->id,
                        'organization_id'           => $organization['organization_id'],
                        'language_id'               => $lang_id
                    ]);
                }
            }
        }
    }

    /**
     * store rounds for comptetion
     * @param \App\Models\Competition  $competition
     * @param array $data
     */
    private function storeRounds(Competition $competition, $data)
    {
        foreach($data['rounds'] as $index=>$round){
            $round['competition_id'] = $competition->id;
            $round['index'] = $index+1;
            $stored_round = Round::create($round);
            foreach($round['levels'] as $round_level){
                $round_level['round_id'] = $stored_round->id;
                RoundLevel::create($round_level);
            }
        }
    }

    /**
     * store awards for comptetion
     * @param \App\Models\Competition  $competition
     * @param array $data
     */
    public function storeAwards(Competition $competition, $data)
    {
        foreach($data['awards'] as $index=>$award){
            $award['competition_id'] = $competition->id;
            if($index === 'overall'){
                $award['is_overall'] = 1;
            }
            Award::create($award);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCompetitionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompetitionRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                if(Arr::has($data, 'global_competition_start_date')){
                    $data['global_competition_start_date'] = Carbon::createFromFormat('m/d/Y', $data['global_competition_start_date'])->format('Y-m-d');
                }
                if(Arr::has($data, 'global_competition_end_date')){
                    $data['global_competition_end_date'] = Carbon::createFromFormat('m/d/Y', $data['global_competition_end_date'])->format('Y-m-d');
                }
                $competition = Competition::create($data);
                
                if(Arr::has($data, 'tags')){
                    foreach($data['tags'] as $tag){
                        DB::table('competition_tag')->insert([
                            'competition_id'       => $competition->id,
                            'tag_id'              => $tag,
                        ]);
                    }
                }
                $this->storeOrganizations($competition, $data);
                $this->storeRounds($competition, $data);
                $this->storeAwards($competition, $data);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        DB::commit();
        return $this->index(new Request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function show(Competition $competition)
    {
        return response($competition->load('organizations', 'tags:id,name', 'rounds', 'rounds.round_level', 'awards'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompetitionRequest  $request
     * @param  \App\Models\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {
        if($request->has('settings')){
            $competition->update($request->all());
            if(Arr::has($request->settings, 'tags')){
                foreach($data['tags'] as $tag){
                    DB::table('competition_tag')->where('competition_id', $competition->id)->delete();
                    DB::table('competition_tag')->insert([
                        'competition_id'       => $competition->id,
                        'tag_id'              => $tag,
                    ]);
                }
            }
        }
        elseif($request->has('organizations')){
            $this->updateComptetionOrganizations($request->organizations, $competition);
        }
        elseif($request->has('rounds')){
            $this->updateRounds($request->rounds, $competition);
        }
        elseif($request->has('awards')){
            $this->updateAwards($request->awards, $competition);
        }
    }

    /**
     * update organizations
     * @param array $organizations
     * @param int $competition_id
     */
    private function updateComptetionOrganizations($organizations, Competition $competition){
        foreach($organizations as $organization){
            if(CompetitionOrganization::where('organization_id', $organization['organization_id'])
                ->where('competition_id', $competition->id)->exists())
            {
                CompetitionOrganization::where('organization_id', $organization['organization_id'])
                ->where('competition_id', $competition->id)->first()->update($organization);
            }else{
                CompetitionOrganization::create($organization);
                if(Arr::has($organization, 'languages_to_translate')){
                    foreach($organization['languages_to_translate'] as $lang_id){
                        DB::table('competition_organization_languages')->insert([
                            'competition_id'            => $competition->id,
                            'organization_id'           => $organization['organization_id'],
                            'language_id'               => $lang_id,
                            'to_view'                   => false
                        ]);
                    }
                }
                if(Arr::has($organization, 'languages_to_view')){
                    foreach($organization['languages_to_view'] as $lang_id){
                        DB::table('competition_organization_languages')->insert([
                            'competition_id'            => $competition->id,
                            'organization_id'           => $organization['organization_id'],
                            'language_id'               => $lang_id
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 
     */
    private function updateRounds($rounds, Competition $competition){
        $competition->rounds()->delete();
        $this->storeRounds($competition, [$rounds]);
    }

    /**
     * 
     */
    private function updateAwards($awards, Competition $competition){
        $competition->awards()->delete();
        $this->storeAwards($competition, [$awards]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competition $competition)
    {
        $competition->deleted_by = auth()->id();
        $competition->save();
        $competition->delete();
    }
}
