<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\FinancialYearFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class FinancialYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    protected $appends = ['start_date_jalali', 'end_date_jalali'];

    /**
     * Get the start date in Jalali format.
     *
     * @return string
     */
    public function getStartDateJalaliAttribute()
    {
        return Jalalian::fromCarbon($this->start_date)->format('Y/m/d');
    }

    /**
     * Get the end date in Jalali format.
     *
     * @return string
     */
    public function getEndDateJalaliAttribute()
    {
        return Jalalian::fromCarbon($this->end_date)->format('Y/m/d');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Clear the cache whenever a financial year is saved or deleted.
        static::saved(function () {
            Cache::forget('active_financial_year');
        });

        static::deleted(function () {
            Cache::forget('active_financial_year');
        });
    }

    /**
     * Get the currently active financial year.
     * Caches the result to improve performance.
     *
     * @return self|null
     */
    public static function getActiveYear()
    {
        // Use 'rememberForever' for efficiency, as the active year changes infrequently.
        // The cache is automatically cleared by the 'booted' method events.
        return Cache::rememberForever('active_financial_year', function () {
            return self::where('is_active', true)->first();
        });
    }

    public static function setActive(self $year): void
    {
        DB::transaction(function () use ($year) {
            self::query()->update(['is_active' => false]);
            $year->update(['is_active' => true]);
        });
    }
}
