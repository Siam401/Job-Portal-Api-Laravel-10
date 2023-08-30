<?php

namespace App\Services\CvParser\Utils;

use Carbon\Carbon;
use Modules\Job\Models\Skill;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
use NlpTools\Tokenizers\WhitespaceTokenizer;

class CommonUtil
{
    public static function getBirthday($text)
    {

        $pattern = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})|([0-9]{2})\.([0-9]{2})\.([0-9]{4})/i';


        preg_match_all($pattern, $text, $matches);

        if (count($matches) > 0) {

            if (isset($matches[0][0])) {
                return self::normalizeBirthDay($matches[0][0]);
            }
        }

        return null;
    }

    public static function getSkillSegmentKeywords()
    {

        return config('segments.skill');
    }

    public static function getEmail(string $text)
    {

        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';

        preg_match_all($pattern, $text, $matches);

        if (count($matches) > 0) {

            if (isset($matches[0][0])) {
                return $matches[0][0];
            }
        }

        return null;
    }

    public static function getPhone(string $text)
    {

        $pattern = "/\d{9,}/i";

        $text = str_replace(array(" ", "-", "(", ")", "/"), array("", "", "", "", ""), $text);

        preg_match_all($pattern, $text, $matches);

        if (count($matches) > 0) {
            if (isset($matches[0][0])) {
                return $matches[0][0];
            }
        }

        return null;
    }

    public static function smallify(string $text) {
        return strtolower(trim(trim($text, ':')));
    }

    public static function getSkills(array $lines)
    {

        $allSkills = Skill::getSkills(); // get skills array

        $skills = [];

        $text = implode(" ", self::getTokens($lines));

        foreach ($allSkills as $skill) {

            if (self::isWordInText($skill, $text)) {

                $skills[] = $skill;
            }
        }

        return $skills;
    }

    public static function getTokens(array $lines, $type = 'whitespace')
    {

        if ($type == 'whitespaceAndPunctuation') {

            $tok = new WhitespaceAndPunctuationTokenizer();
        } else {

            $tok = new WhitespaceTokenizer();
        }

        $tokens = [];

        foreach ($lines as $line) {

            $lineTokens = $tok->tokenize($line);

            if (is_array($lineTokens)) {
                foreach ($lineTokens as $token) {
                    $tokens[] = $token;
                }
            }
        }

        return $tokens;
    }

    public static function normalizeName(string $name)
    {

        $search  = ['Name', ':'];
        $replace = ['', ''];

        $name = str_replace($search, $replace, $name);

        return ucwords(strtolower($name));
    }

    public static function normalizeBirthDay(string $birthday)
    {

        $birthday = str_replace(['/'], ['.'], $birthday);

        return Carbon::parse($birthday)->format('d.m.Y');
    }

    public static function normalizePosition($name)
    {

        return ucwords(strtolower($name));
    }

    public static function getGender(array $lines)
    {
        $tok = new WhitespaceAndPunctuationTokenizer();

        foreach ($lines as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (in_array(strtolower($token), ['male', 'female'])) {
                    return ucfirst($token);
                }
            }
        }

        return null;
    }

    public static function dateRegex()
    {

        $patterns = [];

        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-\—]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sept(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-\—]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sept(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s­]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s­]+(till now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(till now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(ongoing)';
        $patterns[] = '([0-9]{2})\/([0-9]{2})\/([0-9]{4})[\s–\-]+([0-9]{2})\/([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{2})\.([0-9]{2})\.([0-9]{4})[\s–\-]+([0-9]{2})\.([0-9]{2})\.([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+([0-9]{2})\.([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(now)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{4})[\s–\-]+([0-9]{4})';
        $patterns[] = '([0-9]{4})[\s–\—]+([0-9]{4})';
        $patterns[] = '([0-9]{4}) to ([0-9]{4})';
        $patterns[] = '([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{4})[\s–\-]+(until now)';
        $patterns[] = '([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{4})[\s–\-]+(still)';
        $patterns[] = '([0-9]{4})[\s–\-]+(ongoing)';

        $patterns[] = '([0-9]{2})\.[\s]([0-9]{4})[\s–\-]+([0-9]{2})\.[\s]([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{2})\/([0-9]{4})[ to ]+([0-9]{2})\/([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{1})\/([0-9]{2})\/([0-9]{4})[\s]+(to now)';
        //$patterns[] = '([0-9]{4})';

        $pattern = '/' . implode('|', $patterns) . '/i';

        return $pattern;
    }

    public static function isWordInTextSimple($word, $text)
    {
        $patt = "/(?:^|[^a-zA-Z])" . preg_quote($word, '/') . "(?:$|[^a-zA-Z])/i";
        return preg_match($patt, $text);
    }

    public static function isWordInText($word, $text)
    {

        if ((strpos($text, ucfirst($word)) > -1) || (strpos($text, strtoupper($word)) > -1)) {
            return true;
        }

        return false;
    }

    public static function getLanguages(string $text)
    {

        $allLanguages = Skill::getLanguages();

        $languages = [];

        foreach ($allLanguages as $language) {

            if (self::isWordInText($language, $text)) {

                $languages[] = $language;
            }
        }

        return $languages;
    }
}
