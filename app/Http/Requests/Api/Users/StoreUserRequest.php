<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'      => 'nullable|string|max:255',
            'email'     => 'required_without:phone_number|email|nullable|unique:users',
            'password'  => 'required',
            'phone_number'  => 'required_without:email|unique:users',
            'age_group' => 'nullable|string|max:255',
            'gender'    => 'nullable|string|max:255',
            'cover_photo' => 'nullable|image',
            'avatar_photo' => 'nullable|image',
            'bio'       => 'nullable|string|max:140',
            'allow_location_tracking' => 'nullable|bool',
            'post_publicly' => 'nullable|bool',
            't_c_agreed' => 'nullable|bool',
            'profile_visible' => 'nullable|bool',
        ];
    }
}
