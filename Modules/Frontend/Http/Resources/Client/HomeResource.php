<?php

namespace Modules\Frontend\Http\Resources\Client;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'extra' => $this->extra,
            'image' => $this->image ? FileUpload::getUrl($this->image) : null,
            'items' => SectionItemResource::collection($this->sectionItems),
            'is_active' => $this->is_active,
        ];
    }
}