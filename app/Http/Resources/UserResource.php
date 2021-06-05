<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;

class UserResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'verified' => (bool) $this->verified,
            'age_group' => $this->age_group,
            'gender' => $this->gender,
            'cover_photo' => $this->cover_photo ? Storage::disk('s3')->url($this->cover_photo) : null,
            'avatar_photo' => $this->avatar_photo ? Storage::disk('s3')->url($this->avatar_photo) : null,
            'bio' => $this->bio,
            'allow_location_tracking' => (bool) $this->allow_location_tracking,
            'post_publicly' => (bool) $this->post_publicly,
            't_c_agreed' => (bool) $this->t_c_agreed,
            'profile_visible' => (bool) $this->profile_visible,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
        ];
    }
}
