<?php

namespace Modules\Treasury\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Treasury\Database\Factories\ExpenseCategoryFactory;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];
}
