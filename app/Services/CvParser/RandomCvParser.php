<?php

namespace App\Services\CvParser;

use App\Models\Nationality;
use Carbon\Carbon;
use Modules\Job\Models\Skill;
use Modules\Location\Models\Country;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use Web64\LaravelNlp\Facades\NLP;

class RandomCvParser
{
    const MIN_NAME_LENGTH = 6;
    public array $lines = [];
    public array $segments = [];
    public array $data = [];
    public static function parse(string $text)
    {
        $parser = new self;

        $parser->data['fullname'] = $parser->getName($text);
        $parser->data['email'] = $parser->getEmail($text);
        $parser->data['phone'] = $parser->getPhone($text);
        $parser->data['nationality'] = $parser->getNationality($text);
        $parser->data['birthday'] = $parser->getBirthday($text);
        $parser->data['gender'] = $parser->getGender($text);
        $parser->data['linkedin'] = $parser->getLinkedInProfile($text);
        $parser->data['github'] = $parser->getGithubProfile($text);
        $parser->data['skills'] = $parser->getSkills($text);
        $parser->data['languages'] = $parser->getLanguages($text);

        return $parser->data;
    }

    public function getLines($text)
    {
        if (empty($this->lines)) {
            $this->lines = array_values(array_filter(preg_split('/\n|\r\n?/', $text)));
        }
        return $this->lines;
    }

    public function getTokens($text, $type = 'whitespace')
    {

        if ($type == 'whitespaceAndPunctuation') {

            $tok = new WhitespaceAndPunctuationTokenizer();
        } else {

            $tok = new WhitespaceTokenizer();
        }

        $tokens = [];

        $lines = $this->getLines($text);

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

    public function getText($text)
    {

        return implode(" ", $this->getTokens($text));
    }

    public function nGrams($text, $n = 3)
    {

        $tokens = $this->getTokens($text, 'whitespaceAndPunctuation');

        $len = count($tokens);
        $ngram = [];

        for ($i = 0; $i + $n <= $len; $i++) {
            $string = "";
            for ($j = 0; $j < $n; $j++) {
                $string .= " " . $tokens[$j + $i];
            }
            $ngram[$i] = $string;
        }
        return $ngram;
    }

    public function getName($text)
    {

        $userSegment = $this->getLines($text);

        //dd($userSegment);


        $tok = new WhitespaceAndPunctuationTokenizer();

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (strlen($token) > 2) {
                    if (mb_strlen($line) > self::MIN_NAME_LENGTH) {
                        return $this->normalizeName($line);
                    }
                }
            }
        }

        foreach ($userSegment as $line) {

            $entities = NLP::spacy_entities($line, 'en');

            if (!empty($entities)) {
                if (isset($entities['PERSON'])) {
                    if (mb_strlen($line) > self::MIN_NAME_LENGTH) {
                        return $this->normalizeName($line);
                    }
                }
            }
        }

        return null;
    }

    public function getNationality($text)
    {

        $userSegment = $this->getUserSegment($text);

        $nationalities = Country::whereNotNull('nationality')->orWhere('nationality', '<>', '')->pluck('nationality', 'name')->toArray();

        $tok = new WhitespaceAndPunctuationTokenizer();

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (strlen($token) > 3) {
                    if (in_array($token, $nationalities))
                        return $token;
                    elseif (array_key_exists($token, $nationalities))
                        return $nationalities[$token];
                }
            }
        }
        return null;
    }

    public function getBirthday($text)
    {

        $pattern = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})|([0-9]{2})\.([0-9]{2})\.([0-9]{4})/i';

        $userSegment = $this->getUserSegment($text);

        //dd($userSegment);

        foreach ($userSegment as $line) {

            preg_match_all($pattern, $line, $matches);

            if (count($matches) > 0) {

                if (isset($matches[0][0])) {
                    return $this->normalizeBirthDay($matches[0][0]);
                }
            }
        }

        return null;
    }

    public function getGender($text)
    {

        $tok = new WhitespaceAndPunctuationTokenizer();

        $userSegment = $this->getUserSegment($text);

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (in_array(strtolower($token), ['male', 'female'])) {
                    return ucfirst($token);
                }
            }
        }

        return null;
    }

    public function getEmail($text)
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

    public function getPhone($text)
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

    public function getSkills($text)
    {

        $allSkills = Skill::getSkills(); // get skills array

        $skills = [];

        $text = $this->getText($text);

        foreach ($allSkills as $skill) {

            if (self::isWordInText($skill, $text)) {

                $skills[] = $skill;
            }
        }

        return $skills;
    }

    public function getLanguages($text)
    {

        $allLanguages = Skill::getLanguages();

        $languages = [];

        $text = $this->getText($text);

        foreach ($allLanguages as $language) {

            if (self::isWordInText($language, $text)) {

                $languages[] = $language;
            }
        }

        return $languages;
    }

    public function getLinkedInProfile($text)
    {

        $needle = "linkedin.com";

        $tokens = $this->getTokens($text);

        foreach ($tokens as $token) {

            $pos = strpos(strtolower($token), $needle);

            if ($pos > -1) {
                return $token;
            }
        }

        return "";
    }

    public function getGithubProfile($text)
    {

        $needle = "github.com";

        $tokens = $this->getTokens($text);

        foreach ($tokens as $token) {

            $pos = strpos(strtolower($token), $needle);

            if ($pos > -1) {
                return $token;
            }
        }

        return "";
    }


    /* SEGMENTS */

    public function getEducationSegmentKeywords()
    {

        return config('segments.education');
    }

    public function getDegreeSegmentKeywords()
    {

        return config('segments.degree');
    }

    public function getExperienceSegmentKeywords()
    {

        return config('segments.experience');
    }

    public function getSkillSegmentKeywords()
    {

        return config('segments.skill');
    }

    public function getProjectSegmentKeywords()
    {

        return config('segments.project');
    }

    public function getAccomplishmentSegmentKeywords()
    {

        return config('segments.accomplishment');
    }

    public function searchKeywordsInText($keywords, $text)
    {

        if (empty($keywords)) {
            return false;
        }
        foreach ($keywords as $keyword) {
            if (self::isWordInText($keyword, $text)) {
                return true;
            }
        }
        return false;
    }

    public function getUserSegment($text)
    {

        if (!empty($this->segments)) {
            return $this->segments;
        }

        $lines = $this->getLines($text);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $degreeKeywords = $this->getDegreeSegmentKeywords();
        $projectKeywords = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        foreach ($lines as $line) {

            if (
                !$this->searchKeywordsInText($educationKeywords, $line) &&
                !$this->searchKeywordsInText($degreeKeywords, $line) &&
                !$this->searchKeywordsInText($projectKeywords, $line) &&
                !$this->searchKeywordsInText($skillKeywords, $line) &&
                !$this->searchKeywordsInText($accomplishmentKeywords, $line) &&
                !$this->searchKeywordsInText($experienceKeywords, $line)
            ) {
                $this->segments[] = $line;
            } else {
                break;
            }
        }


        return $this->segments;
    }

    public function getEducationSegment($text)
    {

        $segment = [];

        $lines = $this->getLines($text);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $projectKeywords = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        $i = 0;

        foreach ($lines as $line) {

            $i++;
            $flag = false;

            if ($this->searchKeywordsInText($educationKeywords, $line)) {

                $segment[] = $line;
                //$i++;
                $flag = true;

                while ($i < count($lines)) {

                    $row = $lines[$i];

                    if (
                        //!$this->searchKeywordsInText($projectKeywords, $row) &&
                        !$this->searchKeywordsInText($skillKeywords, $row) &&
                        !$this->searchKeywordsInText($accomplishmentKeywords, $row) &&
                        !$this->searchKeywordsInText($experienceKeywords, $row)
                    ) {
                        $segment[] = $row;
                    } else {
                        break;
                    }
                    $i++;
                }
            }

            if ($flag) {
                break;
            }
        }

        return $segment;
    }

    public function getExperienceSegment($text)
    {

        $segment = [];

        $lines = $this->getLines($text);

        //dd($lines);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $degreeKeywords = $this->getDegreeSegmentKeywords();
        //$projectKeywords        = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        $i = 0;
        foreach ($lines as $line) {

            $i++;
            $flag = false;

            if ($this->searchKeywordsInText($experienceKeywords, $line)) {

                $segment[] = $line;
                //$i++;
                $flag = true;

                while ($i < count($lines)) {

                    $row = $lines[$i];

                    if (
                        //!$this->searchKeywordsInText($projectKeywords, $row) &&
                        !$this->searchKeywordsInText($skillKeywords, $row) &&
                        !$this->searchKeywordsInText($accomplishmentKeywords, $row) &&
                        !$this->searchKeywordsInText($educationKeywords, $row) &&
                        !$this->searchKeywordsInText($degreeKeywords, $row)
                    ) {
                        $segment[] = $row;
                        //                        echo $row;
                        //                        echo "<br>";
                    } else {
                        break;
                    }
                    $i++;
                }
            }

            if ($flag) {
                break;
            }
        }
        return $segment;
    }

    public function parseExperienceSegment($text)
    {

        $datesFound = [];
        $positionsFound = [];
        $employersFound = [];


        $positions = []; //Position::getPositions();
        $employers = []; // Employer::getEmployers();
        //dd($employers);

        $experience = [];

        $experienceSegment = $this->getExperienceSegment($text);
        //dd($experienceSegment);


        $pattern = $this->dateRegex();

        $datesSegments = [];
        $i = 0;


        foreach ($experienceSegment as $line) {

            $datesSegments[$i][] = $line;

            preg_match_all($pattern, $line, $matches);

            if (count($matches) > 0) {

                if (isset($matches[0][0])) {
                    $datesFound[] = $matches[0][0];

                    $i++;
                    $datesSegments[$i][] = $line;

                    array_pop($datesSegments[$i - 1]);
                }
            }
        }

        array_shift($datesSegments);

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                foreach ($positions as $position) {

                    if (strpos(ucwords($datesSegments[$i][$j]), $position) > -1) {
                        $positionsFound[] = $position;
                        $flag = true;
                        break;
                    }
                }

                if ($flag) {
                    break;
                }
            }

            if (!$flag) {
                $positionsFound[] = '';
            }
        }

        $companyKeywords = ["name of employer", "company", "employer", 'organization'];
        $replace = ['', '', '', ''];

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                //echo $datesSegments[$i][$j];
                //echo "<br>";
                foreach ($companyKeywords as $comopanyKeyword) {

                    if (strpos(strtolower($datesSegments[$i][$j]), $comopanyKeyword) > -1) {
                        $employersFound[] = preg_replace("/(?![.=$'€%-])\p{P}/u", "", ucwords(trim(str_replace($companyKeywords, $replace, strtolower($datesSegments[$i][$j])))));

                        $flag = true;
                        break;
                    }
                }
                if ($flag)
                    break;
            }

            if (!$flag) {

                for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                    if ($flag) {
                        break;
                    } else {

                        $entities = NLP::spacy_entities($datesSegments[$i][$j], 'en');

                        if (!empty($entities)) {

                            //var_dump($entities);

                            if (isset($entities['ORG'])) {
                                $employersFound[] = $entities['ORG'][0];
                                $flag = true;
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        break;
                    } else {

                        foreach ($employers as $employer) {

                            if (strpos(strtolower($datesSegments[$i][$j]), strtolower(trim($employer))) > -1) {
                                $employersFound[] = $employer;
                                $flag = true;
                                break;
                            }
                        }
                    }
                }
            }

            if (!$flag) {
                $employersFound[] = '';
            }
        }

        //exit;
        //dd($datesSegments);
        //dd($positionsFound);
        //dd($dates);

        $i = 0;
        foreach ($datesFound as $date) {

            $experience[$i]['date'] = $date;
            $experience[$i]['position'] = isset($positionsFound[$i]) ? $positionsFound[$i] : '';
            $experience[$i]['company'] = isset($employersFound[$i]) ? $employersFound[$i] : '';

            $i++;
        }


        return $experience;
    }

    /* NORMALIZE */

    public function normalizeName($name)
    {

        $search = ['Name', ':'];
        $replace = ['', ''];

        $name = str_replace($search, $replace, $name);

        return ucwords(strtolower($name));
    }

    public function normalizeBirthDay($birthday)
    {

        $birthday = str_replace(['/'], ['.'], $birthday);

        return Carbon::parse($birthday)->format('d.m.Y');
    }

    public function normalizePosition($name)
    {

        return ucwords(strtolower($name));
    }

    public function dateRegex()
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
}