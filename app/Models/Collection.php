<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;

class Collection extends BaseModel
{
    use GeneratesUuid, PowerJoins;

    protected $fillable = [
        'name',
        'identifier',
        'time_to_solve',
        'initial_points',
        'recommendations',
        'description',
        'status',
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

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
            if(auth()->user()->hasRole(['super admin', 'admin'])){
                $q->status      = "active";
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

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'collection_tag', 'collection_id', 'tag_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function tasks()
    {
        $sections =  $this->sections()->with('tasks')->get();
        if(empty($sections)){
            return $sections;
        }
        $sections = $sections->map(function ($item, $key){
            if(empty($item->tasks)){
                return [];
            }
            foreach($item->tasks as $task){
                $task->section = 'Section ' . $item->index;
            }
            return $item->tasks;
        })->flatten();
        return $sections;
    }

    public static function applyFilter($filterOptions, $data){
        // filter by domains and tags
        if(isset($filterOptions['tags'])){
            $data = $data->joinRelationship('tags');
            $data = $data->whereIn('domains_tags.id', $filterOptions['tags'])->distinct();
        }

        // filter by status
        if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('collections.status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data){
        return collect([
            'filterOptions' => [
                    'tags'          => $data->get()->pluck('tags')->map->pluck('name', 'id')->unique(),
                    'status'        => $data->pluck("status")->unique()
                ]
        ]);
    }
}
