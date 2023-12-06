<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try{
            $settings = Setting::haveParent()->get();
            foreach ($settings as $setting) {
                config()->set('settings.' . $setting->key, $setting->value);
            }
        }
        catch(\Exception $e){
            //do nothing
        }
    }
}
