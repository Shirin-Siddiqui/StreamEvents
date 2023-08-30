<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'is_read',
        'user_id',
        'eventable_type',
        'eventable_id',
        'created_at',
        'updated_at'
    ];
}
