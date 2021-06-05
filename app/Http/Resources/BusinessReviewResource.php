<?php

namespace App\Http\Resources;

class BusinessReviewResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $business = $this->business;
        unset($this->business_id);
        return array_merge(parent::toArray($request), [
            'business' => new BusinessResource($business)
        ]);
    }
}
