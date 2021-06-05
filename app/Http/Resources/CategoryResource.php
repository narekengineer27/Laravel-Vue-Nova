<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;

class CategoryResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'icon' => $this->icon ? Storage::disk('s3')->url($this->icon) : null
        ]);
    }
}
