<?php

namespace Modules\Treasury\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Treasury\Database\Factories\PayeeFactory;

class Payee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'contact_person', 'phone'];
}
