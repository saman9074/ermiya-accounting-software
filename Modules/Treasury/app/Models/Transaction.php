<?php

namespace Modules\Treasury\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'transactionable_id',
        'transactionable_type',
        'type',
        'amount',
        'transaction_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /**
     * Get the parent transactionable model (invoice, bill, etc.).
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the account that the transaction belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
