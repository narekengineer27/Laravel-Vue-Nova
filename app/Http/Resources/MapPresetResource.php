<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;

class MapPresetResource extends BaseJsonResource
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
          'image' => $this->image ? Storage::disk('s3')->url($this->image) : null,
        ]);
    }
}
