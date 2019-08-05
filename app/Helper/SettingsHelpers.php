<?php

namespace App\Helper;

class SettingsHelpers
{
    function getSetting($key = '')
    {
        $setting = \App\Models\Settings::where('name',strtoupper($key))->first();
        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    }

}