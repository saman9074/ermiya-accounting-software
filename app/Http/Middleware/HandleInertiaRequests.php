<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // <--- ایمپورت کردن کش
use Inertia\Middleware;
use Modules\Core\Models\Setting; // <--- ایمپورت کردن مدل تنظیمات

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // ما منطق جدید را با منطق پیش‌فرض ترکیب می‌کنیم
        return [
            ...parent::share($request),
            'auth' => [
                // به جای ارسال کل آبجکت کاربر، فقط فیلدهای لازم را می‌فرستیم
                'user' => $request->user() ? $request->user()->only('id', 'name', 'email') : null,
            ],
            // این بخش جدید است
            'settings' => function () {
                // برای افزایش سرعت، تنظیمات را در کش ذخیره می‌کنیم
                return Cache::rememberForever('app_settings', function () {
                    return Setting::all()->pluck('value', 'key');
                });
            },
        ];
    }
}

