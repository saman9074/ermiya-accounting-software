<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sales\Database\Factories\SalesReturnFactory;

class SalesReturn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function person() { return $this->belongsTo(\Modules\Persons\Models\Person::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function items() { return $this->hasMany(SalesReturnItem::class); }

    // For polymorphic transaction
    public function transactions()
    {
        return $this->morphMany(\Modules\Treasury\Models\Transaction::class, 'transactionable');
    }
}
