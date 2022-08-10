<?php

namespace App\Http\Controllers;

use App\Models\DifficultyAndPoints;
use App\Models\RoundLevel;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Requests\Competition\StoreDifficultyAndPointsRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DifficultyAndPointsController extends Controller
{
    /**
     * Display a listing of the resource based in RoundLevel (for the editing of RoundLevel).
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RoundLevel $roundLevel)
    {
        return $roundLevel->difficulty_and_points()->with('task')->get();
    }

    /**
     * Display a listing of the resource based on collection (for the add of new RoundLevel).
     *
     * @return \Illuminate\Http\Response
     */
    public function indexBlank(Request $request, Collection $collection)
    {
        return $collection->tasks();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\competition\StoreDifficultyAndPointsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDifficultyAndPointsRequest $request)
    {
        DB::transaction(function () use($request) {
            $identifier = Str::random(16);
            foreach($request->tasks as $data){
                $data['identifier'] = $identifier;
                DifficultyAndPoints::create($data);
            }
        });
        return response(200);
    }
}
