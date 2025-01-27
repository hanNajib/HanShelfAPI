<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Books extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    public $timestamps = false;

    protected $hidden = [
        'deleted_at'
    ];

    /**
     * Get all of the loan for the Books
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrowed(): HasMany
    {
        return $this->hasMany(Loan::class, 'book_id', 'id');
    }

}
