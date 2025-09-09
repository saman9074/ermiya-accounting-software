<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\UnitFactory;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'abbreviation',
    ];
    // protected static function newFactory(): UnitFactory
    // {
    //     // return UnitFactory::new();
    // }
}
