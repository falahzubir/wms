<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct()
    {
        $this->settingsService = new SettingsService();
    }

    public function getSetting($key, $default = null)
    {
        return $this->settingsService->getSetting($key, $default);
    }

    private function setSetting($key, $value)
    {
        $this->settingsService->setSetting($key, $value);
    }

    public function getAllSettings()
    {
        return $this->settingsService->getAllSettings();
    }

    public function index()
    {
        $settings = $this->getAllSettings();
        $title = 'Settings';
        return view('settings.index', compact('settings', 'title'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'setting.*' => 'required'
        ]);

        foreach ($request->setting as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');

    }
}
