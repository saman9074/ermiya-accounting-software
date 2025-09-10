<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sales\Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'person_id',
        'invoice_date',
        'total_amount',
        'paid_amount',
        'payment_status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\Persons\Models\Person::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Modules\Treasury\Models\Transaction::class, 'transactionable');
    }

    /**
     * Define an accessor for the remaining amount.
     * This is a calculated property.
     */
    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_amount - $this->paid_amount,
        );
    }

    // protected static function newFactory(): InvoiceFactory
    // {
    //     // return InvoiceFactory::new();
    // }
}
