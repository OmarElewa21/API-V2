<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class School extends Model
{
    use HasFactory, SoftDeletes, GeneratesUuid;

    protected $fillable = [
        'name',
        'email',
        'province',
        'address',
        'postal_code',
        'phone',
        'country_id',
        'is_tuition_centre',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function created_by(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by_user(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deleted_by(){
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
