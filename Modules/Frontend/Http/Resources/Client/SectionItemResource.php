<?php

namespace Modules\Frontend\Http\Resources\Client;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SectionItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $data = is_string($this->items) ? json_decode($this->items, true) : $this->items;
        if (array_key_exists('image_url', $data)) {
            $data['image_url'] = $data['image_url'] ? FileUpload::getUrl($data['image_url']) : null;
        }
        return $data;
    }
}
