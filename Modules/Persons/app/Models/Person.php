<?php

namespace Modules\Persons\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Persons\Database\Factories\PersonFactory;

class Person extends Model
{
    use HasFactory;


    protected $table = 'persons';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'phone', 'address', 'person_group_id'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PersonGroup::class, 'person_group_id');
    }
}
