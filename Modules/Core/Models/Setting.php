<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key'; // Use 'key' as the primary key
    public $incrementing = false; // The primary key is not auto-incrementing
    protected $keyType = 'string'; // The primary key is a string

    public $timestamps = false; // We don't need created_at and updated_at

    protected $fillable = [
        'key',
        'value',
    ];
}
