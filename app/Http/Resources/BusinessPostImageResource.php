<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BusinessPostImageResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($this->business_post_id);
        return array_merge(parent::toArray($request), [
          'path' => $this->path ? Storage::disk('s3')->url($this->path) : null
        ]);
    }
}
