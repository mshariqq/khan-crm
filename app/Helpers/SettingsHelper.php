<?php

namespace App\Helpers;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value from the database
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Cache all autoload settings for 24 hours
        $settings = Cache::remember('settings_autoload', 86400, function () {
            return Option::where('autoload', 1)
                ->pluck('value', 'name')
                ->toArray();
        });

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        // Fallback: query individually for non-autoloaded settings
        $option = Option::where('name', $key)->first();
        return $option ? $option->value : $default;
    }

    /**
     * Get multiple settings at once
     *
     * @param array $keys
     * @return array
     */
    public static function getMany(array $keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = self::get($key);
        }
        return $results;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param bool $autoload
     * @return bool
     */
    public static function set($key, $value, $autoload = true)
    {
        $updated = Option::updateOrCreate(
            ['name' => $key],
            ['value' => $value, 'autoload' => $autoload ? 1 : 0]
        );

        // Clear cache after updating
        Cache::forget('settings_autoload');

        return $updated !== null;
    }

    /**
     * Check if setting exists
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return Option::where('name', $key)->exists();
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('settings_autoload');
    }
}