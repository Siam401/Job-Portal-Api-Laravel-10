<?php

namespace Modules\Frontend\Http\Resources\Client;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
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
            'title' => $this->title,
            'url' => $this->url,
            'icon_image' => isHTML($this->icon_image) ? $this->icon_image : FileUpload::getUrl($this->icon_image),
            'icon_type' => $this->icon_type
        ];
    }
}
