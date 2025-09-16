import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useCurrency() {
    const page = usePage();
    const activeCurrency = computed(() => page.props.active_currency);

    /**
     * مبلغ را بر اساس ارز فعال فرمت کرده و نام آن را اضافه می‌کند (برای استفاده در لیست‌ها و نمایش).
     * @param {number} amount - مبلغ به ریال (ارز پایه).
     * @returns {string} - رشته فرمت شده به همراه نام ارز (مثال: ۱۰,۰۰۰ تومان).
     */
    const formatCurrency = (amount) => {
        if (amount === null || typeof amount === 'undefined' || isNaN(amount)) {
            amount = 0;
        }

        if (!activeCurrency.value) {
            return new Intl.NumberFormat('fa-IR').format(amount) + ' ریال';
        }

        const value = amount / activeCurrency.value.divisor;
        const formattedValue = new Intl.NumberFormat('fa-IR').format(value);

        // این حالت استاندارد است که بقیه صفحات به آن نیاز دارند
        return `${formattedValue} ${activeCurrency.value.display_name}`;
    };

    /**
     * فقط عدد را بر اساس ارز فعال فرمت می‌کند (برای استفاده در محاسبات فرم‌ها).
     * @param {number} amount - مبلغ به هر واحدی.
     * @returns {string} - رشته عدد فرمت شده (مثال: ۱۰,۰۰۰).
     */
    const formatNumber = (amount) => {
        if (amount === null || typeof amount === 'undefined' || isNaN(amount)) {
            amount = 0;
        }
        return new Intl.NumberFormat('fa-IR').format(amount);
    };


    /**
     * یک مبلغ را از ارز پایه (ریال) به ارز فعال تبدیل می‌کند (برای نمایش در فرم‌ها).
     * @param {number} amountInBase - مبلغ به ریال.
     * @returns {number} - مبلغ تبدیل شده به ارز نمایشی (مثلا تومان).
     */
    const fromBaseCurrency = (amountInBase) => {
        if (!activeCurrency.value || !activeCurrency.value.divisor || activeCurrency.value.divisor <= 1) {
            return amountInBase;
        }
        return amountInBase / activeCurrency.value.divisor;
    };

    return {
        activeCurrency,
        formatCurrency, // <-- برای استفاده در اکثر جاها
        formatNumber,   // <-- تابع جدید فقط برای محاسبات فرم
        fromBaseCurrency,
    };
}
