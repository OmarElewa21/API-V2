<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Http\Requests\CreateSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Support\Str;

class SchoolController extends Controller
{
    /**
     * Display a listing of the schools.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return School::with('country:id,name')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSchoolRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                if(School::withTrashed()->Where('email', $data['email'])->exists()){
                    $school = School::withTrashed()->Where('email', $data['email'])->firstOrFail();
                    $school->update(
                        array_merge($data,
                            ['deleted_at' => null, 'updated_by' => auth()->id(), 'deleted_at' => null]
                        )
                    );
                }else{
                    School::create(
                        array_merge($data, ['created_by' => auth()->id()])
                    );
                }
            } catch (Exception $e) {
                DB::rollBack();
                return response($e->getMessage(), 500);
            }
        }
        DB::commit();
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        return response($school->load('country:id,name'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        $school->update($request->all());
        return response($school->load('country:id,name'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        $school->update(['deleted_by' => auth()->id()]);
        $school->delete();
        return $this->index();
    }

    /**
     * Remove multiple schools.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $school_uuid){
                if(Str::isUuid($school_uuid) && School::whereUuid($school_uuid)->exists()){
                    $school = School::whereUuid($school_uuid)->firstOrFail();
                    $school->update(['deleted_by' => auth()->id()]);
                    $school->delete();
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->index();
    }
}
