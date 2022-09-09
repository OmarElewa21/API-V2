<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Competition extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    const FILTER_COLUMNS = ['name', 'description'];

    protected $fillable = [
        'name',
        'identifier',
        'global_competition_start_date',
        'global_competition_end_date',
        're_run',
        'format',
        'difficulty_group_id',
        'grades',
        'instructions',
        'status',
        'competition_reference',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'uuid'                          => EfficientUuid::class,
        'grades'                        => AsArrayObject::class,
        'global_competition_start_date' => 'datetime:Y/m/d',
        'global_competition_end_date'   => 'datetime:Y/m/d'
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function tags()
    {
        return $this->belongsToMany(DomainsTags::class, 'collection_tag', 'collection_id', 'tag_id');
    }

    public function organizations()
    {
        return $this->hasMany(CompetitionOrganization::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    public function overall_award()
    {
        return $this->hasMany(Award::class)->where('is_overall', 1);
    }

    public function round_awards()
    {
        return $this->hasMany(Award::class)->where('is_overall', 0);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public static function getFilterForFrontEnd($filter){
        return collect([
            'filterOptions' => [
                    'format'    => $filter->pluck('format')->unique()->values(),
                    'tag'       => $filter->get()->pluck('tags')->unique()
                                        ->filter(function ($value, $key) {
                                            return count($value) !== 0;
                                    })->flatten()->pluck('name', 'id'),
                    'status'    => $filter->pluck('status')->unique()->values()
                ]
            ]);
    }

    public static function applyFilter(Request $request, $data){   
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);

            if(isset($filterOptions['format']) && !is_null($filterOptions['format'])){
                $data->where('format', $filterOptions['format']);
            }
    
            if(isset($filterOptions['tags']) && !is_null($filterOptions['tags'] && is_array($filterOptions['tags']))){
                foreach($filterOptions['tags'] as $tag_id){
                    $data->whereRelation('tags', 'id', $tag_id);
                }
            }
    
            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $data->where('status', $filterOptions['status']);
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data->where(function($query)use($search){
                $query->where('competitions.name', 'LIKE', '%'. $search. '%');
                foreach(self::FILTER_COLUMNS as $column){
                    $query->orwhere('competitions.' . $column, 'LIKE', '%'. $search. '%');
                }
            });
        }

        return $data;
    }
}
