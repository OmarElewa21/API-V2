<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['super admin', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $validation_arr = [];
        if($this->has('settings')){
            $validation_arr = [
            'settings'                                 => 'array',
            'settings.name'                            => 'required|string|max:132',
            'settings.tags'                            => 'array',
            'settings.instructions'                    => 'string',
            'settings.global_competition_start_date'   => 'date',
            'settings.global_competition_end_date'     => 'date',
            'settings.re_run'                          => 'boolean',
            'settings.format'                          => 'string|in:Local,Global',
            'settings.grades'                          => 'required|array',
            'settings.difficulty_group_id'             => ['required',
                                                            Rule::exists(\App\Models\DifficultyGroup::class, 'id')
                                                            ->whereNull('deleted_at')->where('status', 'Active')],
            ];
            
            return $validation_arr;
        }

        if($this->has('organizations')){
            foreach($this->organizations as $k=>$organization){
                $validation_arr = array_merge($validation_arr, [
                    'organizations.'.$k.'.organization_id'                   => ['required', Rule::exists(\App\Models\User::class, 'id')->whereNull('deleted_at')],
                    'organizations.'.$k.'.allow_session_edits_by_partners'   => 'boolean',
                    'organizations.'.$k.'.registration_open'                 => 'required|date',
                    'organizations.'.$k.'.competition_dates'                 => 'required|string',
                    'organizations.'.$k.'.languages_to_view'                 => 'array',
                    'organizations.'.$k.'.languages_to_translate'            => 'array',
                ]);
            }
    
            return $validation_arr;
        }

        if($this->has('rounds')){
            foreach($this->rounds as $k=>$round){
                $validation_arr = array_merge($validation_arr, [
                    'rounds.'.$k.'.label'                             => 'required',
                    'rounds.'.$k.'.configurations'                    => 'required|in:Team,Individual',
                    'rounds.'.$k.'.one_account_answer_tasks'          => 'boolean',
                    'rounds.'.$k.'.tasks_assigned_by_leader'          => 'boolean',
                    'rounds.'.$k.'.free_for_all'                      => 'boolean',
                    'rounds.'.$k.'.contribute_to_individual_score'    => 'required|boolean',
                ]);
            }
    
            return $validation_arr;
        }

        if($this->has('awards')){
            foreach($this->awards as $k=>$award){
                $validation_arr = array_merge($validation_arr, [
                    'awards.'.$k.'.by_position'                             => 'boolean|required',
                    'awards.'.$k.'.use_grade_to_assign_points'              => 'boolean',
                    'awards.'.$k.'.min_points'                              => 'numeric',
                    'awards.'.$k.'.use_min_points_for_all'                  => 'boolean',
                    'awards.'.$k.'.default_award'                           => 'string',
                    'awards.'.$k.'.labels'                                  => 'required|array',
                    'awards.'.$k.'.percentage'                              => 'numeric',
                ]);
            }
    
            return $validation_arr;
        }
    }
}
