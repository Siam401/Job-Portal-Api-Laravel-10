<?php

namespace Modules\Frontend\Http\Resources\Client;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeWingCityItemResource extends JsonResource
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
            "id" => $this->code ?? $this->slug ?? $this->id,
            "name" => $this->name,
            "logo" => $this->logo ? FileUpload::getUrl($this->logo) : null,
            "job_count" => $this->job_count
        ];
    }
}
