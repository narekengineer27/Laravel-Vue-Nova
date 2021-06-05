<?php

namespace App\Http\Requests\Api\BusinessOwnership;

use Illuminate\Foundation\Http\FormRequest;

class StoreOwnershipRequest extends FormRequest
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
            'method' => 'required|in:email,phone,support',
//            'address' => 'required|string',
//            'user_info' => 'required|string'
        ];
    }
}
