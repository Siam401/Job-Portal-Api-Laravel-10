<?php

namespace App\Services\CvParser;

use App\Services\CvParser\Utils\CommonUtil;

final class BdJobsCvParser
{
    public array $tags = [];

    public array $segments = [];
    public array $data = [];

    public function __construct()
    {
        $this->tags = config('parser.cv');
    }

    public static function parse(array $textArray)
    {
        $parser = new self;
        $parser->segments = $textArray;

        foreach ($parser->tags as $value) {
            try {
                $parser = call_user_func([$parser, 'get' . $value]);
            } catch (\Throwable $e) {
                continue;
            }
        }

        // dd($parser->data, $parser->segments);
        return $parser->data;
    }

    private function getName()
    {
        $this->data['name'] = $this->segments[0];
        unset($this->segments[0], $this->tags['name']);
        return $this;
    }

    private function getAddress()
    {

        if(!strstr($this->segments[2], 'mobile')) {
            $this->segments[1] .= $this->segments[2];
            unset($this->segments[2]);
        }

        $tmp = explode(':', $this->segments[1]);
        $this->data['address'] = isset($tmp[1]) ? trim($tmp[1]) : null;
        unset($this->segments[1], $this->tags['address']);
        return $this;
    }

    private function getMobile()
    {
        $mobile = '';
        foreach ($this->segments as $key => $value) {
            if(strstr(strtolower($value), 'mobile')) {
                $mobile = $value;
                break;
            }
        }

        $tmp = explode(':', $mobile);
        $this->data['phone'] = isset($tmp[1]) ? trim($tmp[1]) : null;
        unset($this->tags['phone']);
        return $this;
    }

    private function getEmail()
    {
        $email = '';
        foreach ($this->segments as $key => $value) {
            if(strstr(strtolower($value), 'email')) {
                $email = $value;
                break;
            }
        }

        $tmp = explode(':', $email);
        $this->data['email'] = isset($tmp[1]) ? trim($tmp[1]) : null;
        unset($this->tags['email']);
        return $this;
    }

    private function getCareerObjective()
    {
        return $this->tagConcatinator('career objective');
    }

    private function getCareerSummary()
    {
        return $this->tagConcatinator('career summary');
    }

    private function getSpecialQualification()
    {
        $this->tagConcatinator('special qualification');
        $this->data['special qualification'] = CommonUtil::getSkills([$this->data['special qualification']]);
        return $this;
    }

    private function getAcademicQualification()
    {
        return $this->tagConcatinator('academic qualification');
    }

    private function getEmploymentHistory()
    {
        return $this->tagConcatinator('employment history');
    }

    private function getTrainingSummary()
    {
        return $this->tagConcatinator('training summary');
    }

    private function getApplicationInformation()
    {
        $tempText = $this->tagConcatinator('application information')->data['application information'];
        $tempText = str_replace(' :', ':', $tempText);

        $dKeys = ['available for', 'present salary', 'expected salary'];
        $result = $this->parseByKeys($dKeys, $tempText);

        $result['present salary'] = filter_var($result['present salary'], FILTER_SANITIZE_NUMBER_INT);
        $result['expected salary'] = filter_var($result['expected salary'], FILTER_SANITIZE_NUMBER_INT);

        $this->data['application information'] = $result;

        return $this;
    }

    private function parseByKeys(array $keys, string $text)
    {
        $result = [];
        foreach ($keys as $i => $val) {
            if (!isset($keys[$i + 1])) {
                $result[$val] = explode(':', $text)[1] ?? '';
            } else {
                $startPos = stripos($text, ':');
                $endPos = stripos($text, $keys[$i + 1]);
                $result[$val] = trim(str_replace(':', '', substr($text, $startPos, $endPos - $startPos)));
                $text = substr_replace($text, '', 0, $endPos);
            }
        }

        return $result;
    }

    private function getSpecialization()
    {
        $this->tagConcatinator('specialization');
        $this->data['specialization'] = CommonUtil::getSkills([$this->data['specialization']]);
        return $this;
    }

    private function getLanguageProficiency()
    {
        $this->tagConcatinator('language');
        $this->data['language'] = CommonUtil::getLanguages($this->data['language']);
        return $this;
    }

    private function getPersonalDetails()
    {
        $tempText = $this->tagConcatinator('personal details')->data['personal details'];
        $tempText = str_replace(' :', ':', $tempText);

        $dKeys = ['father', 'mother', 'date', 'gender', 'marital', 'nationality', 'religion', 'permanent', 'current'];
        $result = $this->parseByKeys($dKeys, $tempText);

        $this->data['personal details'] = $result;

        return $this;
    }

    private function getReferences()
    {
        return $this->tagConcatinator('reference');
    }

    private function tagConcatinator($tag)
    {
        $startKey = null;

        foreach ($this->segments as $key => $line) {
            $temp = strtolower(trim(trim($line, ':')));
            if (empty($startKey) && ($temp === $tag || str_contains($temp, $tag))) {
                $startKey = $key + 1;
                unset($this->tags[$tag], $this->segments[$key]);
                break;
            }
        }

        while ($startKey && !array_key_exists(CommonUtil::smallify($this->segments[$startKey]), $this->tags)) {
            $this->data[$tag] = !isset($this->data[$tag]) ? $this->segments[$startKey] : $this->data[$tag] . ' ' . $this->segments[$startKey];
            unset($this->segments[$startKey]);
            $startKey++;
        }

        $this->segments = array_values($this->segments);
        return $this;
    }
}
