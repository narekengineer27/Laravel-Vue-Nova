<?php

namespace App\Http\Requests\Api\Businesses;

use App\Rules\Lat;
use App\Rules\Lng;
use Illuminate\Foundation\Http\FormRequest;

class BookmarkBusiness extends FormRequest
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
            'uuid' => 'required|string'
        ];
    }
}


