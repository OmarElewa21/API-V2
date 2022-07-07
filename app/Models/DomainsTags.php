<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;

class DomainsTags extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'name',
        'is_tag',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    function __construct()
    {
        parent::__construct();
        $this->hidden[] = 'pivot';
    }

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

    public function scopeDomains($query)
    {
        return $query->where('is_tag', 0);
    }

    public function scopeTags($query)
    {
        return $query->where('is_tag', 1);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class, 'domain_id');
    }

    public static function applyFilter($filterOptions)
    {
        $data = new DomainsTags;
        if(isset($filterOptions['type']) && !is_null($filterOptions['type'])){
            switch ($filterOptions['type']) {
                case 'Domain':
                    $data = $data->domains();
                    break;
                case 'Tag':
                    $data = $data->tags();
                    break;
                default:
                    break;
            }
        }
        elseif(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data)
    {
        return collect([
            'filterOptions' => [
                    'status'   => $data->pluck('status')->unique(),
                    'type'     => $data->selectRaw("CASE WHEN is_tag=1 THEN 'Tag' ELSE 'Domain' END AS type")->distinct()->pluck('type')
                ]
            ]);
    }
}
