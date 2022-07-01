<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\GeneratesUuid;
use Kirschbaum\PowerJoins\PowerJoins;

class DifficultyGroup extends BaseModel
{
    use SoftDeletes, GeneratesUuid, PowerJoins;

    protected $fillable = [
        'name',
        'has_default_marks',
        'created_by',
        'updated_by',
        'deleted_by',
        'status'
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function levels()
    {
        return $this->hasMany(DifficultyGroupLevel::class);
    }

    public static function applyFilter($filterOptions)
    {
        $data = self::withTrashed();
        if(isset($filterOptions['status']) && !is_null($filterOptions['status'])){
            $data = $data->where('status', $filterOptions['status']);
        }
        return $data;
    }

    public static function getFilterForFrontEnd($data)
    {
        return collect([
            'filterOptions' => [
                    'status'   => $data->pluck('status')->unique(),
                ]
            ]);
    }
}
