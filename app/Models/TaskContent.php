<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kirschbaum\PowerJoins\PowerJoins;

class TaskContent extends Model
{
    use HasFactory, PowerJoins;

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'lang_id',
        'title',
        'content',
        'updated_by',
        'updated_at'
    ];

    protected $hidden = ['updated_at'];

    protected $casts = [
        'updated_at'    => 'date:Y-m-d H:i:s',
    ];

    protected function updatedBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['updated_at'])) . ')' : $value
        );
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
