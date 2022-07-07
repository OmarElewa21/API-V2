<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAnswer extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'is_img',
        'order',
        'is_correct',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public static function booted()
    {
        parent::booted();

        static::creating(function($q) {
            $q->created_by = auth()->id();
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function contents()
    {
        $this->hasMany(TaskAnswerContent::class, 'answer_id');
    }
}
