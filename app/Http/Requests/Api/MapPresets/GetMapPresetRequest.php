<?php

namespace App\Http\Requests\Api\MapPresets;

use Illuminate\Foundation\Http\FormRequest;

class GetMapPresetRequest extends FormRequest
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
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'map_preset_uuid' => 'required|string',
        ];
    }
}
