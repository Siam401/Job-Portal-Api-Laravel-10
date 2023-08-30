<?php

namespace Modules\Frontend\Http\Resources\Client;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Resources\Json\JsonResource;

class LayoutResource extends JsonResource
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
            "header" => [
                "site_title" => $this['header']['site_title'],
                "site_description" => $this['header']['site_description'],
                "favicon" => $this['header']['site_favicon'] ? FileUpload::getUrl($this['header']['site_favicon']) : null,
            ],
            "logo" => $this['logo'] ? FileUpload::getUrl($this['logo']) : null,
            "footer" => [
                'footer_text' => $this['footer']['footer_text'],
                'footer_logo' => $this['footer']['footer_logo'] ? FileUpload::getUrl($this['footer']['footer_logo']) : null,
            ],
            "theme" => $this['theme'],
            "seo" => $this['seo'],
            "social_links" => SocialLinkResource::collection($this['social_links'])
        ];
    }
}
