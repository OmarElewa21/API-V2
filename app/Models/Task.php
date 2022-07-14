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

    public function domainsOnly()
    {
        return $this->belongsToMany(DomainsTags::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\DomainsTags')->wherePivot('is_tag', 0);
    }

    public function domains()
    {
        $topics_Ids = $this->topics()->pluck('id');
        return $this->domainsOnly()->with(['topics' => function($q) use($topics_Ids){
            $q->whereIn('id', $topics_Ids);
        }]);
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
        return $this->belongsToMany(Topic::class, 'task_domains', 'task_id', 'relation_id')->wherePivot('relation_type', 'App\Models\Topic');
    }
    
    public function task_content()
    {
        return $this->hasMany(TaskContent::class);
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
                foreach($filterOptions['domains'] as $domain_uuid){
                    $filterIds[] = DomainsTags::whereUuid($domain_uuid)->value('id');
                }
            }
            if(isset($filterOptions['tags'])){
                foreach($filterOptions['tags'] as $domain_uuid){
                    $filterIds[] = DomainsTags::whereUuid($domain_uuid)->value('id');
                }
            }
            $data = $data->whereIn('domains_tags.id', $filterIds)->distinct();
        }

        // filter by status
        if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('tasks.status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data){
        return collect([
            'filterOptions' => [
                    'lang_count'    => $data->pluck('task_content_count')->unique(),
                    'domain'        => $data->get()->pluck('domains')->map->pluck('name', 'uuid')->unique(),
                    'tags'          => $data->get()->pluck('tags')->map->pluck('name', 'uuid')->unique(),
                    'status'        => $data->pluck("status")->unique()
                ]
        ]);
    }
}
