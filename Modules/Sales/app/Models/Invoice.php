<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sales\Database\Factories\InvoiceFactory;
use Modules\Sales\Models\SalesReturn;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'person_id',
        'issue_date',
        'due_date',
        'subtotal_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
    ];

    protected $with = ['person', 'items'];
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
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

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }
    protected $appends = ['translated_status'];

    public function getTranslatedStatusAttribute(): string
    {
        return match ($this->status) {
            'unpaid' => 'پرداخت نشده',
            'paid' => 'پرداخت شده',
            'partially_paid' => 'پرداخت ناقص',
            'returned' => 'مرجوعی',
            default => $this->status,
        };
    }

    public function updateStatus()
    {
        // رفرش کردن مدل برای دریافت آخرین داده‌ها
        $this->refresh();

        // ۱. مجموع تمام پرداخت‌های مثبت (دریافت وجه)
        $totalPayments = $this->transactions()->where('type', 'income')->where('amount', '>', 0)->sum('amount');

        // ۲. مجموع کل مبالغ مرجوعی
        $totalReturned = $this->salesReturns()->sum('total_amount');

        // ۳. مبلغ پرداختی نهایی فاکتور (پرداختی‌ها منهای برگشتی‌ها)
        $this->paid_amount = $totalPayments - $totalReturned;

        // ۴. تعیین وضعیت فاکتور با اولویت‌بندی صحیح
        // اولویت اول: آیا فاکتور به طور کامل مرجوع شده است؟
        if (abs($totalReturned - $this->total_amount) < 0.01) {
            $this->status = 'returned';
        }
        // اولویت دوم: آیا فاکتور به طور کامل پرداخت شده است؟
        elseif (abs($this->paid_amount - $this->total_amount) < 0.01) {
            $this->status = 'paid';
        }
        // اولویت سوم: آیا بخشی از مبلغ پرداخت شده است؟
        elseif ($this->paid_amount > 0.001) {
            $this->status = 'partially_paid';
        }
        // در غیر این صورت، فاکتور پرداخت نشده است
        else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}
