<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use stdClass;

class Setting extends Model
{

    protected $fillable = [
        'name',
        'value',
        'data_type',
    ];

    /**
     * If company type is Group of Companies
     *
     * @return boolean
     */
    public static function isGroupCompany(): bool
    {
        return Cache::rememberForever('setting_is_group_company', function () {
            return isTrue(self::where('name', 'is_group_company')->first()?->value);
        });
    }

    /**
     * If company type is NGO
     *
     * @return boolean
     */
    public static function isNgoCompany(): bool
    {
        return Cache::rememberForever('setting_is_ngo', function () {
            return isTrue(self::where('name', 'is_ngo')->first()?->value);
        });
    }

    /**
     * Does the company has branches
     *
     * @return boolean
     */
    public static function isBranchEnabled(): bool
    {
        return Cache::rememberForever('setting_has_branch', function () {
            return isTrue(self::where('name', 'has_branch')->first()?->value);
        });
    }

    public static function getAll()
    {
        return Cache::remember("settings", 1200, function () {
            $options = new stdClass;
            foreach (Setting::get() as $data) {
                $options->{$data->name} = dataConvert($data->value, $data->data_type);
            }
            return $options;
        });
    }

    /**
     * Get Settings value by key
     *
     * @param string $key
     * @param [any] $default
     * @return string|array|null
     */
    public static function getOption(string $key, $default = null)
    {
        $data = Cache::remember("setting_{$key}", 1200, function () use ($key) {
            $option = Setting::where('name', $key)->first();
            return isset($option->value) ? dataConvert($option->value, $option->data_type) : null;
        });

        if (empty($data) && !is_null($default)) {
            return $default;
        } else {
            return $data;
        }
    }

    /**
     * Update Settings Meta Values by Array of Keys
     *
     * @param array $info
     * @return void
     */
    public static function updateArray(array $inputs)
    {
        foreach ($inputs as $key => $input) {
            if (!is_numeric($key) && (is_string($input) || is_null($input))) {
                $setting = Setting::updateOrCreate(
                    ['name' => $key],
                    ['value' => $input, 'data_type' => 'string']
                );
            } else {
                $setting = Setting::updateOrCreate(
                    ['name' => $input['name']],
                    [
                        'value' => dataConvert($input['value'], $input['type'] ?? $input['data_type']),
                        'data_type' => $input['type'] ?? $input['data_type']
                    ]
                );
            }

            Cache::put('setting_' . $key, $setting->value, 1200);
        }

        Cache::forget('settings');

        return;
    }

    /**
     * Save Site Meta Value by Key
     *
     * @param string $key
     * @param string|null $value
     * @param boolean $isFile
     * @param string $label
     * @return Setting
     */
    public static function saveOption(string $key, string $value = null, string $type = 'string')
    {
        $option = Setting::updateOrCreate([
            'name' => $key,
        ], [
            'value' => $value,
            'data_type' => $type
        ]);

        Cache::put('setting_' . $key, dataConvert($value, $type), 1200);
        return $option;
    }
}