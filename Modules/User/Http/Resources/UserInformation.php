<?php

namespace Modules\User\Http\Resources;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Applicant\Http\Resources\ApplicantResource;

class UserInformation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $responseData = [
            'role' => $this->user_type,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'photo' => $this->photo ? FileUpload::getUrl($this->photo) : null,
        ];

        if($this->user_type === 'applicant') {
            $responseData['applicant'] = new ApplicantResource($this->applicant);
        }

        return $responseData;
    }
}