<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = ['user_id', 'permission_id'];

    protected $fillable = ['user_id', 'permission_id'];
}
