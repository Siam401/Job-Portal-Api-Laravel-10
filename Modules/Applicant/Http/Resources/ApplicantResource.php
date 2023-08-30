<?php

namespace Modules\Applicant\Http\Resources;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\JobApplication\Models\JobApplication;

class ApplicantResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'resume' => $this->resume ? FileUpload::getUrl($this->resume) : null,
            'photo' => $this->photo ? FileUpload::getUrl($this->photo) : null,
            'is_favorite' => boolval($this->is_favorite),
            'applied_jobs' => JobApplication::getAppliedJobCodes($this->id),
        ];
    }
}