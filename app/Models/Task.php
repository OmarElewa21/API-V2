<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;

class Task extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'title',
        'identifier',
        'description',
        'img',
        'solution_working',
        'recommendations',
        'status',
        'answer_type',
        'answer_layout',
        'answer_structure',
        'answer_sorting',
        'answers_as_img',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'uuid'              => EfficientUuid::class,
        'recommendations'   => AsArrayObject::class,
    ];

    protected $appends = ['languages'];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
            if(auth()->user()->hasRole(['super admin', 'admin'])){
                $q->status      = "approved";
                $q->approved_by = auth()->id();
                $q->approved_at = now();
            }
        });
    }

    protected function approvedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['approved_at'])) . ')' : $value
        );
    }

    public function getCompetitionsAttribute(){
        $sections = $this->sections;
        if(count($sections) > 0){
            $competitions = [];
            foreach($sections as $section){
                $roundLevels = $section->collection->roundLevels;
                if(count($roundLevels) > 0){
                    $competitions[] = $roundLevels->pluck('round')->pluck('competition');
                }
            }
            return $competitions[0];
        }
        return null;
    }

    public function getLanguagesAttribute()
    {
        $langs = $this->task_content()->joinRelationship('language')->select('task_contents.status', 'languages.name')->get();
        $langs = $langs->mergeRecursive([
            'pending' => $this->task_content()->where('status', 'pending')->count(),
            'total'   => $this->task_content()->count(),
        ]);
        return $langs->all();
    }

    public function domains()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags')->wherePivot('is_tag', 0)->whereNull('parent_id');;
    }

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags')->wherePivot('is_tag', 1);
    }

    public function domains_and_tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags');
    }

    public function topics()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('is_tag', 0)->whereNotNull('parent_id');
    }
    
    public function task_content()
    {
        return $this->hasMany(TaskContent::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class)->withPivot('index', 'in_group', 'group_index');
    }

    public function task_answers()
    {
        return $this->hasMany(TaskAnswer::class);
    }

    public static function applyFilter($filterOptions, $data){
        // filter by lang_count
        if(isset($filterOptions['lang_count']) && !is_null($filterOptions['lang_count'])){
            $data = $data->having('task_content_count', $filterOptions['lang_count']);
        }

        // filter by domains and tags
        if(isset($filterOptions['domain']) || isset($filterOptions['tags'])){
            $data = $data->joinRelationship('domains_and_tags');
            $filterIds = [];
            if(isset($filterOptions['domains'])){
                $filterIds = array_merge($filterIds, $filterOptions['domains']);
            }
            if(isset($filterOptions['tags'])){
                $filterIds = array_merge($filterIds, $filterOptions['tags']);
            }
            $data = $data->whereIn('domains_tags.id', $filterIds)->distinct();
        }

        // filter by competition
        if(isset($filterOptions['competition']) && !is_null($filterOptions['competition'])){
            $data = $data->where('tasks.status', $filterOptions['status']);
        }

        // filter by status
        if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('tasks.status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data){
        $data = $data->get();
        return collect([
            'filterOptions' => [
                    'lang_count' => $data->pluck('task_content_count')->unique(),

                    'domain' 
                        => $data->pluck('domains')->flatten()->map(function ($item, $key) {
                            return ['id' => $item['id'], 'name' => $item['name']];
                        })->unique(),

                    'tags' 
                        => $data->pluck('tags')->flatten()->map(function ($item, $key) {
                            return ['id' => $item['id'], 'name' => $item['name']];
                        })->unique(),

                    'status'    => $data->pluck("status")->unique()
                ]
        ]);
    }
}
