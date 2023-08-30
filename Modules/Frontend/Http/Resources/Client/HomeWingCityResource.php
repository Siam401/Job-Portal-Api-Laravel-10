<?php

namespace Modules\Frontend\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeWingCityResource extends JsonResource
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
            'title' => $this['title'],
            'subtitle' => $this['subtitle'],
            'description' => $this['description'],
            'items' => HomeWingCityItemResource::collection($this['items']),
            'is_active' => $this['is_active']
        ];
    }
}
