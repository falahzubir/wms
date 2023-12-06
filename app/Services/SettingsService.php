<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{

    public function getAllSettings()
    {
        return Setting::whereNull('parent_id')->with('children')->get();
    }

    public function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public function setSetting($key, $value, $parent_id = null)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value], ['parent_id' => $parent_id]);
    }
}
