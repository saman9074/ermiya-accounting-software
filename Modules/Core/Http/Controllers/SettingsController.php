<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Currency;
use Illuminate\Support\Facades\DB;


class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Core::Settings/Index', [
            'settings' => Setting::all()->pluck('value', 'key'),
            'currencies' => Currency::all(), // <-- ارسال لیست ارزها
            'active_currency_id' => Currency::where('is_active', true)->first()?->id, // <-- ارسال ارز فعال
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:191',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'company_logo' => 'nullable|image|max:1024',
            'default_print_size' => 'nullable|string|in:A4,A5,Thermal',
            'active_currency_id' => 'required|exists:currencies,id',
        ]);

        // Update or create text-based settings
        foreach ($validated as $key => $value) {
            if ($key !== 'company_logo') {
                Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            }
        }

        DB::transaction(function () use ($validated) {
            Currency::query()->update(['is_active' => false]);
            Currency::find($validated['active_currency_id'])->update(['is_active' => true]);
        });

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete the old logo if it exists
            $oldLogoPath = Setting::where('key', 'company_logo_path')->value('value');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            // Store the new logo and save its path
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'company_logo_path'], ['value' => $path]);
        }
        Cache::forget('app_settings');

        return redirect()->route('settings.index')->with('success', 'تنظیمات با موفقیت ذخیره شد.');
    }
}

