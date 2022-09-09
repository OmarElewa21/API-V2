<?php

namespace App\Http\Requests\Competition;

use App\Http\Requests\CreateBaseRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreCompetitionRequest extends CreateBaseRequest
{
    function __construct()
    {
        $this->key = 'competition';
        $this->unique_fields = ['identifier'];
    }

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
     * @return arr of rules
     */
    protected function validationRules($key)
    {
        $validation_arr = [
            $key.'.name'                            => 'required|string|max:132',
            $key.'.identifier'                      => 'required|unique:competitions,identifier',
            $key.'.tags'                            => 'array',
            $key.'.instructions'                    => 'string',
            $key.'.global_competition_start_date'   => 'date',
            $key.'.global_competition_end_date'     => 'date',
            $key.'.re_run'                          => 'boolean',
            $key.'.format'                          => 'string|in:Local,Global',
            $key.'.grades'                          => 'required|array',
            $key.'.difficulty_group_id'             => ['required',
                                                            Rule::exists(\App\Models\DifficultyGroup::class, 'id')
                                                            ->whereNull('deleted_at')->where('status', 'Active')],
            $key.'.organizations'                   => 'required|array',
            $key.'.rounds'                          => 'required|array',
            $key.'.awards'                          => 'required|array',
        ];

        $validation_arr = $this->tags_validation($key, $validation_arr);
        $validation_arr = $this->organizations_validation($key, $validation_arr);
        $validation_arr = $this->rounds_validation($key, $validation_arr);
        $validation_arr = $this->awards_validation($key, $validation_arr);

        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function tags_validation($key, $validation_arr)
    {
        if(Arr::has($this->get($key), 'tags')){
            foreach($this->get($key)['tags'] as $k=>$tag){
                $validation_arr = array_merge($validation_arr, [
                    $key.'.tags.'.$k      => Rule::exists(\App\Models\DomainsTags::class, 'id')->where('is_tag', 1)->where('status', 'Approved')->whereNull('deleted_at')
                ]);
            }
        }
        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function organizations_validation($key, $validation_arr)
    {
        foreach($this->get($key)['organizations'] as $k=>$organization){
            $validation_arr = array_merge($validation_arr, [
                $key.'.organizations.'.$k.'.organization_id'                   => ['required', Rule::exists(\App\Models\Organization::class, 'id')->whereNull('deleted_at')],
                $key.'.organizations.'.$k.'.allow_session_edits_by_partners'   => 'boolean',
                $key.'.organizations.'.$k.'.registration_open'                 => 'required|date',
                $key.'.organizations.'.$k.'.competition_dates'                 => 'required|string',
                $key.'.organizations.'.$k.'.languages_to_view'                 => 'array',
                $key.'.organizations.'.$k.'.languages_to_translate'            => 'array',
            ]);
        }

        return $validation_arr;
    }

    /**
     * @return (array) rules
     */
    private function rounds_validation($key, $validation_arr)
    {
        foreach($this->get($key)['rounds'] as $k=>$round){
            $validation_arr = array_merge($validation_arr, [
                $key.'.rounds.'.$k.'.label'                             => 'required',
                $key.'.rounds.'.$k.'.configurations'                    => 'required|in:Team,Individual',
                $key.'.rounds.'.$k.'.one_account_answer_tasks'          => 'boolean',
                $key.'.rounds.'.$k.'.tasks_assigned_by_leader'          => 'boolean',
                $key.'.rounds.'.$k.'.free_for_all'                      => 'boolean',
                $key.'.rounds.'.$k.'.contribute_to_individual_score'    => 'required|boolean',
            ]);
        }

        return $validation_arr;
    }

    private function awards_validation($key, $validation_arr)
    {
        foreach($this->get($key)['awards'] as $k=>$award){
            $validation_arr = array_merge($validation_arr, [
                $key.'.awards.'.$k.'.by_position'                             => 'boolean|required',
                $key.'.awards.'.$k.'.use_grade_to_assign_points'              => 'boolean',
                $key.'.awards.'.$k.'.min_points'                              => 'numeric',
                $key.'.awards.'.$k.'.use_min_points_for_all'                  => 'boolean',
                $key.'.awards.'.$k.'.default_award'                           => 'string',
                $key.'.awards.'.$k.'.labels'                                  => 'required|array',
                $key.'.awards.'.$k.'.percentage'                              => 'numeric',
            ]);
        }

        return $validation_arr;
    }
}
