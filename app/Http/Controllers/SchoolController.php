<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Http\Requests\CreateSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Scopes\UserScope;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    /**
     * Display a listing of the schools.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        if($request->has('filterOptions')){
            $request->validate([
                'filterOptions'                 => 'array',
                'filterOptions.type'            => ['string', Rule::in(['school', 'tuition_centre'])],
                'filterOptions.country'         => 'exists:countries,id',
                'filterOptions.status'          => ['string', Rule::in(['pending', 'approved', 'rejected', 'deleted'])]
            ]);
            $data = School::applyFilter($request->get('filterOptions'));
        }else{
            $data = new School;
        }
        $data = collect(
                    $data->withTrashed()
                        ->with(['country:id,name', 'teachers' => function($teacher) {
                            $teacher->withoutGlobalScopes([UserScope::class])->select('school_id','user_id');
                        }, 'teachers.user:id,name,uuid'])
                        ->withCount('teachers')
                        ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                    )
                    ->merge([
                        'pending' => School::pending()->count()
                    ])
                    ->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']);
        return response($data, 200);
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
                            ['deleted_at' => null, 'updated_by' => auth()->id(), 'deleted_by' => null]
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
        return $this->index(new Request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        return response(
            $school->load(['country:id,name', 'teachers' => function($teacher) {
            $teacher->withoutGlobalScopes([UserScope::class])->select('school_id','user_id');
        }, 'teachers.user:id,name,uuid'])->loadCount('teachers'),
        200);
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
        return response(
            $school->load(['country:id,name', 'teachers' => function($teacher) {
                $teacher->withoutGlobalScopes([UserScope::class])->select('school_id','user_id');
            }, 'teachers.user:id,name,uuid'])->loadCount('teachers'),
            200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        $school->update(['deleted_by' => auth()->id(), 'status' => 'deleted']);
        $school->delete();
        return $this->index(new Request);
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
                    $school->update(['deleted_by' => auth()->id(), 'status' => 'deleted']);
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
        return $this->index(new Request);
    }
}
