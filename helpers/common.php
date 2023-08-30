<?php

use App\Services\FileUpload\FileUpload;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Modules\Settings\Models\Setting;

if (!function_exists('slug')) {
    /**
     * Create slug from text (version 1)
     *
     * @param string $text
     * @param bool $random
     * @return string
     */
    function slug(string $text, bool $random = false): string
    {
        return Str::slug($text) . ($random ? rand(1111, 9999) : '');
    }
}

if (!function_exists('reverseSlug')) {
    /**
     * Convert word(s) from slug
     *
     * @param string $text
     * @param bool $hasUnderscore
     * @return string
     */
    function reverseSlug(string $text, bool $hasUnderscore = false): string
    {
        $value = ucwords(str_replace('-', ' ', $text));
        if($hasUnderscore) {
            $value = ucwords(str_replace('_', ' ', $value));
        }
        return $value;
    }
}



if (!function_exists('isHTML')) {
    /**
     * Check string is HTML code or not
     *
     * @param string $string
     * @return boolean
     */
    function isHTML(string $string): bool
    {
        return $string != strip_tags($string) ? true : false;
    }
}

if (!function_exists('generateVerifyCode')) {
    /**
     * Get random code as verfication code
     *
     * @param integer $length
     * @return integer
     */
    function generateVerifyCode(int $length): int
    {
        if ($length == 0)
            return 0;
        $min = pow(10, $length - 1);
        $max = (int) ($min - 1) . '9';
        return random_int($min, $max);
    }
}

if (!function_exists('generateNumber')) {
    /**
     * Generate fixed length random number
     *
     * @param integer $length
     * @return integer|string
     */
    function generateNumber(int $length = 8): int|string
    {
        $characters = '1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('generateTrxNumber')) {
    /**
     * Generate fixed length transaction number
     *
     * @param integer $length
     * @param string $prefix
     * @return string
     */
    function generateTrxNumber(int $length = 12, string $prefix = ''): string
    {
        $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $prefix . $randomString;
    }
}

if (!function_exists('getAmount')) {
    /**
     * Get amount of fixed length precision
     *
     * @param float $amount
     * @param integer $length
     * @return float
     */
    function getAmount(float $amount, int $length = 2): float
    {
        $amount = round($amount, $length);
        return $amount + 0;
    }
}

if (!function_exists('removeElement')) {
    /**
     * Remove array element by value without changing index order
     *
     * @param array $arr
     * @param mixed $value
     * @return array
     */
    function removeElement(array $arr, $value): array
    {
        return array_diff($arr, (is_array($value) ? $value : array($value)));
    }
}

if (!function_exists('keyToTitle')) {
    /**
     * Key slug to title conversion
     *
     * @param string $text
     * @return string
     */
    function keyToTitle(string $text): string
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}

if (!function_exists('keyToTitle')) {
    /**
     * Title to key slug conversion
     *
     * @param string $text
     * @return string
     */
    function titleToKey(string $text): string
    {
        return strtolower(str_replace(' ', '_', $text));
    }
}

if (!function_exists('isTrue')) {
    /**
     * Check value is true or false
     *
     * @param any $value
     * @return bool
     */
    function isTrue($value): bool
    {
        if (is_string($value)) {
            $value = trim($value);
            return in_array($value, ['1', 'true', 'ok', 'on', 'yes']);
        } else if (is_bool($value)) {
            return $value;
        } else if (is_numeric($value)) {
            return intval($value) > 0;
        } elseif (is_array($value)) {
            return count($value) > 0;
        } else {
            return false;
        }
    }
}

if (!function_exists('diffForHumans')) {
    /**
     * Return date/datetime difference from current time as human readable format
     *
     * @param string|Carbon $date
     * @param string $tz Timezone
     * @return string
     */
    function diffForHumans(string|Carbon $date, string $tz = null): string
    {

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        if ($tz) {
            $date = $date->setTimezone($tz);
        }
        return $date->diffForHumans();
    }
}

if (!function_exists('showDateTime')) {
    /**
     * Translate date time with given format
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    function showDateTime(string $date, string $format = 'Y-m-d h:i A'): string
    {
        $lang = session()->get('lang');
        Carbon::setlocale($lang);
        return Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('showMobileNumber')) {
    /**
     * Get mobile number with hidden asteriks
     *
     * @param string|integer $number
     * @return string
     */
    function showMobileNumber(string|int $number): string
    {
        $length = strlen($number);
        return substr_replace($number, '***', 2, $length - 4);
    }
}

if (!function_exists('showEmailAddress')) {
    /**
     * Get email address with hidden asteriks
     *
     * @param string $email
     * @return string
     */
    function showEmailAddress(string $email): string
    {
        $endPosition = strpos($email, '@') - 1;
        return substr_replace($email, '***', 1, $endPosition);
    }
}

if (!function_exists('getRealIP')) {
    /**
     * Get real IP address of server request
     *
     * @return string
     */
    function getRealIP(): string
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        //Deep detect ip
        if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        }
        if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }
}

if (!function_exists('slugify')) {
    /**
     * Create slug from text (version 2)
     *
     * @param string $string
     * @param integer $length
     * @return string
     */
    function slugify(string $string, int $length = 50): string
    {
        $length = $length < 5 ? 5 : $length;

        if (strlen($string) > $length) {
            $string = fewWords($string, $length);
        }

        $string = preg_replace('/\s+/', ' ', strtolower($string));

        return trim(str_replace(' ', '-', $string));
    }
}

if (!function_exists('fewWords')) {
    /**
     * Get 1st n characters of words
     *
     * @param string $message Original message text
     * @param integer $K Number of characters
     * @return string Truncated words
     */
    function fewWords(string $message, int $K = 20, string $postFix = '')
    {

        if ($K < 1) {
            return '';
        }

        if (strlen($message) <= $K) {
            return trim($message);
        }

        if ($message[$K] === " ") {
            return trim(substr($message, 0, $K));
        }

        while ($message[--$K] !== ' ')
            ;

        return trim(substr($message, 0, $K)) . $postFix;
    }
}

if (!function_exists('hex2rgb')) {
    /**
     * Hex color code to RGB code conversion
     *
     * @param string $color
     * @return string
     */
    function hex2rgb($color): string
    {
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        return "$r, $g, $b";
    }
}

if (!function_exists('callApi')) {
    /**
     * CURL POST request api call
     *
     * @param string $url
     * @param array|object|string $params
     * @return object|string|null
     */
    function callApi(string $url, $params)
    {
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}

if (!function_exists('getCookie')) {
    /**
     * Get cookie data
     *
     * @param string $key
     * @param any $default
     * @return any
     */
    function getCookie(string $key, $default = null)
    {
        return Cookie::get($key, $default);
    }
}

if (!function_exists('rmdirRecursive')) {
    /**
     * Remove a directory with it's file recusrively
     *
     * @param string $dir
     * @return bool
     */
    function rmdirRecursive(string $dir)
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file)
                continue;
            if (is_dir("$dir/$file"))
                rmdirRecursive("$dir/$file");
            else
                unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}

if (!function_exists('days')) {
    /**
     * Get days array
     *
     * @return array
     */
    function days(): array
    {
        return [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday'
        ];
    }
}

if (!function_exists('isApplicantUser')) {
    /**
     * Check session user is applicant
     *
     * @return bool
     */
    function isApplicantUser(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->user_type === 'applicant';
    }
}

if (!function_exists('formatBdMobileNumber')) {
    /**
     * Format BD mobile number
     *
     * @param string $mobile
     * @return string
     */
    function formatBdMobileNumber(string $value)
    {
        $mobile = str_replace(['+', '-', ' ', '(', ')'], '', $value);

        if (substr($mobile, 0, 2) === '88') {
            $mobile = substr($mobile, 2);
        } elseif (substr($mobile, 0, 1) !== '0') {
            $mobile = '0' . $mobile;
        }
        return $mobile;
    }
}

if (!function_exists('dataConvert')) {
    /**
     * Convert data to specific type
     *
     * @param [any] $data
     * @param string $type
     * @return integer|float|object|string|boolean|array
     */
    function dataConvert($data, string $type)
    {
        $type = $type ?? 'string';

        if (empty($data)) {
            return dataDefault($type);
        }

        if ($type === 'int' || $type === 'integer' || $type === 'number') {
            return intval($data);
        } elseif ($type === 'float') {
            return floatval($data);
        } elseif ($type === 'json' || $type === 'array') {
            return json_decode(trim($data));
        } elseif ($type === 'serialize') {
            return unserialize(trim($data));
        } elseif ($type === 'bool' || $type === 'boolean') {
            return isTrue($data);
        } elseif ($type === 'comma') {
            return explode(',', $data);
        } elseif ($type === 'url') {
            return filter_var($data, FILTER_VALIDATE_URL) ? $data : '';
        } else {
            return gettype($data) === 'string' ? $data : (string) $data;
        }
    }
}

if (!function_exists('dataDefault')) {
    /**
     * Get default value for specific type
     */
    function dataDefault(string $type)
    {
        if ($type === 'int' || $type === 'integer') {
            return 0;
        } elseif ($type === 'float') {
            return 1.0;
        } elseif ($type === 'json') {
            return json_decode(json_encode([]));
        } elseif ($type === 'serialize' || $type === 'comma') {
            return [];
        } elseif ($type === 'bool') {
            return false;
        } elseif ('string' === $type) {
            return '';
        } else {
            return null;
        }
    }
}

if (!function_exists('uploadFile')) {
    /**
     * Process file upload
     *
     * @param UploadedFile $file
     * @param string $type
     * @param boolean $mustRequire
     * @return string|null
     * @throws HttpResponseException
     */
    function uploadFile(UploadedFile $file, string $type = 'file', bool $mustRequire = false): string|null
    {
        $fileUpload = FileUpload::instance()->upload($file);

        if (empty($fileUpload) || is_string($fileUpload)) {

            if ($mustRequire) {
                throw new HttpResponseException(
                    response()->json([
                        'success' => false,
                        'result_code' => 1,
                        'message' => $fileUpload ? $fileUpload : 'Error! Failed to upload ' . $type
                    ], 400)
                );

            }

        }

        return $fileUpload['path'] ?? null;
    }
}

if (!function_exists('getFile')) {
    /**
     * Get file url if exists
     *
     * @param string $file
     * @return string|null
     */
    function getFile(string $file = null): string|null
    {
        if (empty($file)) {
            return null;
        }
        return FileUpload::getUrl($file);
    }
}

if (!function_exists('sortMulti')) {
    /**
     * Sort multidimensional array by numeric value
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    function sortMulti(array $array, string $key): array
    {
        usort($array, function ($a, $b) use($key) {
            if ($a[$key] > $b[$key]) {
                return 1;
            } elseif ($a[$key] < $b[$key]) {
                return -1;
            }
            return 0;
        });

        return $array;
    }
}

if (!function_exists('getSetting')) {
    /**
     * Get setting value by key
     *
     * @param string $key
     * @return mixed
     */
    function getSetting(string $key): mixed
    {
        return Setting::getOption($key);
    }
}