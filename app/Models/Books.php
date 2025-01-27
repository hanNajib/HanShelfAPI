<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Books extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    public $timestamps = false;

    protected $hidden = [
        'deleted_at'
    ];
}
