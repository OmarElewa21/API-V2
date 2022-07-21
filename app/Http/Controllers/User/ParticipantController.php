<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Http\Requests\User\StoreParticipantRequest;
use App\Http\Requests\User\UpdateParticipantRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Get User model with the correct data based on role
     * @return Builder query
     */
    protected function indexfilterByRole($data){
        switch (auth()->user()->role->name) {
            case 'country partner':
                $data->where('country_partner_id', auth()->id());
                break;

            case 'country partner assistant':
                    $data->where('country_partner_id', auth()->user()->countryPartnerAssistant->country_partner_id);
                break;

            case 'school manager':
                $data->where('school_id', auth()->user()->schoolManager->school_id);
                break;
    
            case 'teacher':
                $data->where('school_id', auth()->user()->teacher->school_id);
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Participant::leftJoinRelationship('school')->leftJoinRelationship('countryPartner')
                    ->leftJoinRelationshipUsingAlias('tuition_centre', 'tuition_centre')
                    ->leftJoinRelationship('country')
                    ->select('participants.*', 'schools.name as school','users.name as partner',
                                'countries.name as country', 'tuition_centre.name as tuition_centre');
        
        $this->indexfilterByRole($data);
        
        $data = Participant::applyFilter($request, $data);
        
        $filterOptions = Participant::getFilterForFrontEnd($data);

        return response($filterOptions->merge($data
                ->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreParticipantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreParticipantRequest $request)
    {
        DB::beginTransaction();
        foreach($request->all() as $key=>$data){
            try {
                switch (auth()->user()->role->name) {
                    case 'country partner':
                        $data['country_partner_id'] = auth()->id();
                        break;
                    case 'country partner assistant':
                        $data['country_partner_id'] = auth()->user()->countryPartnerAssistant->country_partner_id;
                        break;
                    case 'school manager':
                        $schoolManager = auth()->user()->schoolManager;
                        $data['country_partner_id'] = $schoolManager->country_partner_id;
                        $data['school_id'] = $schoolManager->school_id;
                        break;
                    case 'teacher':
                        $teacher = auth()->user()->teacher;
                        $data['country_partner_id'] = $teacher->country_partner_id;
                        $data['school_id'] = $teacher->school_id;
                        break;
                    default:
                        break;
                }
                $data['country_id'] = User::find($data['country_partner_id'])->country_id;
                Participant::create($data);
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
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function show(Participant $participant)
    {
        $participant = Participant::whereUuid($participant->uuid)->leftJoinRelationship('school')->leftJoinRelationship('countryPartner')
                            ->leftJoinRelationshipUsingAlias('tuition_centre', 'tuition_centre')
                            ->leftJoinRelationship('country')
                            ->select('participants.*', 'schools.name as school','users.name as partner',
                                        'countries.name as country', 'tuition_centre.name as tuition_centre')
                            ->firstOrFail();
        return response($participant, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateParticipantRequest  $request
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        $participant->update($request->all());
        return $this->show($participant);
    }

    /**
     * regenerate Password for the participant
     */
    public function regenerate_password(Participant $participant)
    {
        $password = Str::random(14);
        $participant->password = encrypt($password);
        $participant->save();
        return response($password, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Participant $participant)
    {
        $participant->delete();
        return $this->index(new Request);
    }

    /**
     * mass delete users
     */
    public function mass_delete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $participant_uuid){
                if(Str::isUuid($participant_uuid)){
                    $participant = Participant::whereUuid($participant_uuid)->firstOrFail();
                    $participant->delete();
                }else{
                    throw new \Exception("data is not valid");
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
        DB::commit();
        return $this->index(new Request);
    }
}
