<?php

namespace Modules\Persons\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Models\PriceList;

class PersonGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price_list_id'];

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }
}
