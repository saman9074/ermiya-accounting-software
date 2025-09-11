<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        // Retrieve all settings and format them as a simple key => value array
        $settings = Setting::all()->pluck('value', 'key');

        return Inertia::render('Core::Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:191',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'company_logo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        // Update or create text-based settings
        foreach ($validated as $key => $value) {
            if ($key !== 'company_logo') {
                Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            }
        }

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

        return redirect()->route('settings.index')->with('success', 'تنظیمات با موفقیت ذخیره شد.');
    }
}

