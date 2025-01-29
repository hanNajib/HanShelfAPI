<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelf extends Model
{
    protected $guarded = [];
    protected $table = 'shelf';

    /**
     * Get all of the books for the Shelf
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Books::class, 'shelf_id', 'id');
    }
}
