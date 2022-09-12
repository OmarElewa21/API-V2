<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\RoundLevel;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoundLevel $round_level, Request $request)
    {
        $headerData = RoundLevel::where('round_levels.id', $round_level->id)->joinRelationship('round')->joinRelationship('round.competition')
                        ->leftJoinRelationship('sessions')
                        ->select('round_levels.id', 'round_levels.level as level', 'rounds.index as round', 'competitions.name as competition',
                                DB::raw('DATE_FORMAT(competitions.global_competition_start_date, "%Y/%m/%d") as start_date'),
                                DB::raw('DATE_FORMAT(competitions.global_competition_end_date, "%Y/%m/%d") as end_date'));

        $data = Session::distinct()->where('sessions.round_level_id', $round_level->id)->withCount('participants');

        Session::applyFilter($request, $data);

        $filterOptions = Session::getFilterForFrontEnd($data);        // get collection of availble filter options data

        return $filterOptions->merge(
                collect(["headerData" => $headerData->get()])
                ->merge(
                    collect($data->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5))
                )
            )
            ->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSessionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoundLevel $round_level, StoreSessionRequest $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $data){
                Session::create(array_merge($data, ['round_level_id' => $round_level->id]));
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->index($round_level, new Request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function show(Session $session)
    {
        return $session->load('round_level');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSessionRequest  $request
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSessionRequest $request, Session $session)
    {
        $session->update($request->all());
        return $this->show($session);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(Session $session)
    {
        $session->delete();
        return $this->index($session->round_level, new Request);
    }
}
