<?php

namespace Modules\Job\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Company\Services\JobCompanyService;
use Modules\Job\Models\Job;
use Modules\Job\Models\JobQuestion;
use Modules\Settings\Services\FormOptionService;

class JobDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'seo' => [
                'title' => $this->title . ' | ' . ($this->wing ? $this->wing->name : $this->branch?->name),
                'description' => mb_substr(strip_tags($this->detail->responsibility), 0, 160),
                'image' => $this->company_summary['wing_logo'] ?? $this->company_summary['company_logo'],
                'keywords' => Job::getKeywords($this->company_summary['wing_code'] ?? $this->company_summary['company_code']),
            ],
            'detail' => [
                'title' => $this->title,
                'company' => $this->wing ? $this->wing->name : $this->branch?->name,
                'job_type' => FormOptionService::getOptionTitle('job_type', $this->detail->job_type),
                'address' => $this->wing ? $this->wing->address : $this->branch?->address,
                'description' => $this->detail->description,
                'responsibility' => $this->detail->responsibility,
                'education' => $this->detail->education,
                'benefit' => $this->detail->benefit,
                'additional' => $this->detail->additional,
                'experience' => isTrue($this->detail->is_exp_required) ? $this->detail->experience : null,
                'salary' => $this->detail->salary,
                'skills' => json_decode($this->detail->skills, true),
            ],
            'summary' => [
                'start_date' => Carbon::parse($this->start_date)->format('j M Y'),
                'vacancy' => $this->vacancy,
                'job_type' => FormOptionService::getOptionTitle('job_type', $this->detail->job_type),
                'is_experience_required' => isTrue($this->detail->is_exp_required),
                'min_experience' => $this->detail->min_exp,
                'max_experience' => $this->detail->max_exp,
                'min_age' => $this->detail->age_min,
                'max_age' => $this->detail->age_max,
                'salary' => $this->detail->salary,
                'end_date' => Carbon::parse($this->end_date)->format('j M Y'),
                'workplace' => $this->detail->workplace,
            ],
            'company' => $this->company_summary,
            'form' => [
                'inputs' => $this->detail->form_visibility,
                'questions' => JobQuestion::select('question', 'is_required')->whereIn('id', $this->detail->questions)->get()->toArray(),
            ]
        ];
    }
}
