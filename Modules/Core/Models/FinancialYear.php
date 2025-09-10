<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\FinancialYearFactory;
use Illuminate\Support\Facades\Cache;
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
}
