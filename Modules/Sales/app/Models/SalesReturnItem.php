<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sales\Database\Factories\SalesReturnItemFactory;

class SalesReturnItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function product() { return $this->belongsTo(\Modules\Inventory\Models\Product::class); }
    public function salesReturn() { return $this->belongsTo(SalesReturn::class); }
}
