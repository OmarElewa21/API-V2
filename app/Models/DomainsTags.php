<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Support\Arr;

class DomainsTags extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;
    
    const FILTER_COLUMNS = ['domains_tags.name'];

    const STATUS = [
        "Approved"          => 1,
        "Pending"           => 2,
        "Deleted"           => 3
    ];

    protected $fillable = [
        'name',
        'is_tag',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'parent_id'
    ];

    function __construct()
    {
        parent::__construct();
        $this->hidden[] = 'pivot';
    }

    public static function booted()
    {
        parent::booted();

        static::creating(function($model) {
            $model->created_by = auth()->id();
            if(auth()->user()->hasRole(['super admin', 'admin'])){
                $model->status      = self::STATUS['Approved'];
                $model->approved_by = auth()->id();
                $model->approved_at = now();
            }else{
                $model->status      = self::STATUS['Pending'];
            }
        });

        static::updating(function($model) {
            $model->updated_by = auth()->id();
        });
    }

    protected function approvedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['approved_at'])) . ')' : $value
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => array_search($value, self::STATUS) ? array_search($value, self::STATUS) : $value
        );
    }

    public function scopeDomains($query)
    {
        return $query->where('is_tag', 0)->whereNull('parent_id');
    }

    public function scopeTags($query)
    {
        return $query->where('is_tag', 1);
    }

    public function scopeTopics($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function domain()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getTopicsAttribute(){
        return self::where('parent_id', $this->id)->select('id', 'uuid', 'name')->get();
    }

    public static function applyFilter(Request $request, $data)
    {
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);
            if(isset($filterOptions['type']) && !is_null($filterOptions['type'])){
                switch ($filterOptions['type']) {
                    case 'Topic':
                        $data = $data->topics();
                        break;
                    case 'Tag':
                        $data = $data->tags();
                        break;
                    default:
                        break;
                }
            }

            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                if(array_key_exists($filterOptions['status'], self::STATUS)){
                    $data = $data->where('status', self::STATUS[$filterOptions['status']]);
                }
            }
        }

        if($request->filled('search')){
            $search = $request->search;
            $data = $data->where(function($query)use($search){
                $query->where('name', 'LIKE', '%'. $search. '%');
            });
        }

        return $data;
    }

    public static function getFilterForFrontEnd($data)
    {
        $statuses = $data->pluck('status')->unique()->toArray();
        return collect([
            'filterOptions' => [
                    'status'   => array_keys(Arr::where(self::STATUS, function ($value, $key) use($statuses){
                                        return in_array($value, $statuses);
                                    })),
                    'type'     => $data->selectRaw("CASE WHEN is_tag=1 THEN 'Tag' ELSE 'Topic' END AS type")->pluck('type')->unique()->values()
                ]
            ]);
    }
}
