<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Core\Models\FinancialYear;

class DateWithinFinancialYear implements ValidationRule
{
    /**
     * The active financial year instance.
     *
     * @var \Modules\Core\Models\FinancialYear|null
     */
    protected $activeYear;

    public function __construct()
    {
        // Get the active financial year once and reuse it.
        $this->activeYear = FinancialYear::getActiveYear();
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->activeYear) {
            $fail('هیچ سال مالی فعالی تعریف نشده است. لطفا ابتدا یک سال مالی را فعال کنید.');
            return;
        }

        $date = \Illuminate\Support\Carbon::parse($value);

        if (!$date->betweenIncluded($this->activeYear->start_date, $this->activeYear->end_date)) {
            $fail("تاریخ انتخاب شده خارج از محدوده سال مالی فعال (" . jdate($this->activeYear->start_date)->format('Y/m/d') . " تا " . jdate($this->activeYear->end_date)->format('Y/m/d') . ") است.");
        }
    }
}
