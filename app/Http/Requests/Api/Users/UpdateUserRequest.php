<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'sometimes|string|max:255',
            'email'     => 'sometimes|required_without:phone_number|email|unique:users,email,'. auth()->id(),
            'phone_number'     => 'sometimes|required_without:email|unique:users,phone_number,'. auth()->id(),
            'age_group' => 'sometimes|string|max:255',
            'gender'    => 'sometimes|string|max:255',
            'cover_photo' => 'sometimes|image',
            'avatar_photo' => 'sometimes|image',
            'bio'       => 'sometimes|string|max:140',
            'allow_location_tracking' => 'sometimes|bool',
            'post_publicly' => 'sometimes|bool',
            't_c_agreed' => 'sometimes|bool',
            'profile_visible' => 'sometimes|bool',
        ];
    }
}
