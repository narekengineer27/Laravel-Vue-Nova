<?php

namespace App\Http\Requests\Api\BusinessReviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessReview extends FormRequest
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
            'score'       => 'required|integer|between:0,100',
            'comment'     => 'nullable|string',
            'review_photo'   => 'sometimes|image',
            'mode' => 'required|boolean'
        ];
    }
}
