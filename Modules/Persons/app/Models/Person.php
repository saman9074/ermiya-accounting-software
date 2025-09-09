<?php

namespace Modules\Persons\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Persons\Database\Factories\PersonFactory;

class Person extends Model
{
    use HasFactory;


    protected $table = 'persons';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'phone', 'address'];

    // protected static function newFactory(): PersonFactory
    // {
    //     // return PersonFactory::new();
    // }
}
