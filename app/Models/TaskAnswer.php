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
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function contents()
    {
        $this->hasMany(TaskAnswerContent::class, 'answer_id');
    }
}
