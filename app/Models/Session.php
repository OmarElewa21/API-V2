<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;

class Session extends BaseModel
{
    use SoftDeletes, GeneratesUuid;

    const FILTER_COLUMNS = ['name', 'status'];

    protected $fillable = [
        'name',
        'round_level_id',
        'is_default',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function round_level()
    {
        return $this->belongsTo(RoundLevel::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'round_level_participant');
    }

    public static function applyFilter(Request $request, $query)
    {
        if($request->has('filterOptions') && gettype($request->filterOptions) === 'string'){
            $filterOptions = json_decode($request->filterOptions, true);
            if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
                $query->where('sessions.status', $filterOptions['status']);
            }
        }
        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($query)use($search){
                $query->where('name', 'LIKE', '%'. $search. '%');
                foreach(self::FILTER_COLUMNS as $column){
                    $query->orwhere($column, 'LIKE', '%'. $search. '%');
                }
            });
        }
    }

    public static function getFilterForFrontEnd($data){
        return collect([
            'filterOptions' => [
                    'status'    => $data->pluck('status')->unique()
                ]
            ]);
    }
}
