<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $appName = Setting::get('app_name', config('app.name'));
        return view('admin.settings.index', compact('appName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'website_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        Setting::set('app_name', $request->app_name);

        if ($request->hasFile('website_logo')) {
            $path = $request->file('website_logo')->store('assets/logo', 'public');
            Setting::set('website_logo', $path);
        }

        return back()->with('success', 'Application settings updated successfully!');
    }
}
