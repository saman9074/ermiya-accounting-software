<?php

namespace Modules\Treasury\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Treasury\Database\Factories\AccountFactory;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'initial_balance',
        'current_balance',
        'description',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];
}
