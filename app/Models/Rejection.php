<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Rejection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = [
        'relation_id',
        'relation_type',
        'user_id',
        'created_at'
    ];

    protected $fillable = [
        'created_by',
        'relation_id',
        'relation_type',
        'reason',
        'count',
        'user_id',
        'created_at'
    ];

    protected $casts = [
        'created_at'    => 'date:Y-m-d H:i:s',
    ];

    protected function createdBy(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $value ? User::find($value)->name . ' (' . date('d/m/Y H:i', strtotime($attributes['created_at'])) . ')' : $value
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function relation()
    {
        return $this->morphTo();
    }
}
