<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RoundLevelParticipant;

class UpdateRoundLevelParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'mode'          => 'required|in:team,session,status',
            'participants'  => 'required|array'
        ];

        if(is_array($this->participants)){
            foreach($this->participants as $key=>$participant_id){
                $rules = array_merge($rules, [
                    'participants.' . $key  => 'exists:participants,id'
                ]);
            }
        }
        
        switch ($this->mode) {
            case 'team':
                $additional_rules = [
                    'team' => 'required|exists:competition_teams,id'
                ];
                break;
            case 'session':
                $additional_rules = [
                    'session' => 'required|exists:sessions,id'
                ];
                break;
            case 'status':
                $additional_rules = [
                    'status' => 'required|in:' . implode(',', RoundLevelParticipant::STATUSSES)
                ];
                break;
            
            default:
                $additional_rules = [];
                break;
        }
        
        return array_merge($rules, $additional_rules);
    }
}
