<?php

namespace App\Http\Requests\Api\Businesses;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessCoverPhoto extends FormRequest
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
            'cover_photo' => 'required|image'
        ];
    }
}
