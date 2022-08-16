<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionPartner;
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
                'partners' => function($query){
                    $query->joinRelationship('partner')->joinRelationship('partner.country')
                        ->select('competition_partners.competition_id', 'competition_partners.partner_id', 'users.name as partner_name', 'countries.name');
                },
                'tags:id,name',
                'rounds' => function($query){
                    $query->joinRelationship('round_level')->joinRelationship('round_level.collection')
                        ->select('rounds.id', 'rounds.competition_id', 'round_levels.level', 'round_levels.grades', 'collections.name');
                },
                'round_awards:id,competition_id,labels'
            ])->withCount('partners', 'tags', 'rounds');
        
        Competition::applyFilter($request, $data);

        $filterOptions = Competition::getFilterForFrontEnd($data);
        
        return response($filterOptions->merge($data
                ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']), 200); 
    }


    /***************************************** Storing *****************************************/
    /**
     * store partner for competition
     * @param \App\Models\Competition  $competition
     * @param array $data
     */
    private function storePartners(Competition $competition, $data)
    {
        foreach($data['partners'] as $partner){
            $partner['competition_dates'] = 
                Arr::map(explode('-', $partner['competition_dates']), function ($value, $key) {
                    return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
                });
            $partner['competition_id'] = $competition->id;
            $partner['registration_open'] = Carbon::createFromFormat('m/d/Y', $partner['registration_open'])->format('Y-m-d');
            
            $competition_parnter = CompetitionPartner::create($partner);

            if(Arr::has($partner, 'languages_to_translate')){
                foreach($partner['languages_to_translate'] as $lang_id){
                    DB::table('competition_parnter_languages')->insert([
                        'competition_partner_id'    => $competition_parnter->id,
                        'language_id'               => $lang_id,
                        'to_view'                   => false
                    ]);
                }
            }
            if(Arr::has($partner, 'languages_to_view')){
                foreach($partner['languages_to_view'] as $lang_id){
                    DB::table('competition_parnter_languages')->insert([
                        'competition_partner_id'    => $competition_parnter->id,
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
                $this->storePartners($competition, $data);
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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatecompetitionRequest  $request
     * @param  \App\Models\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competition $competition)
    {
        //
    }
}
