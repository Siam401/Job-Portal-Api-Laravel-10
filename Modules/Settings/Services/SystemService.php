<?php

namespace Modules\Settings\Services;


final class SystemService
{

    /**
     * Update .env file values
     *
     * @param array $values
     * @return bool
     */
    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $envKey = strtoupper($envKey);
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= $envKey . '=' . (!empty($envValue) || isTrue($envValue) ? "'{$envValue}'\n" : "\n");
                } else {
                    $str = str_replace($oldLine, $envKey . '=' . (!empty($envValue) || isTrue($envValue) ? "'{$envValue}'" : ""), $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }
}