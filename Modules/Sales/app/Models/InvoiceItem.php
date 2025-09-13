<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sales\Database\Factories\InvoiceItemFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_price',
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\Inventory\Models\Product::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // protected static function newFactory(): InvoiceItemFactory
    // {
    //     // return InvoiceItemFactory::new();
    // }
}
