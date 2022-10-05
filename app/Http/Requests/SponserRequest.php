<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SponserRequest extends FormRequest
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
            'title' => 'required|string|max:40',
            'body' => 'required|string|max:2000',
            'image' => 'required|file|image|mimes:jpg,png',
            'category_id' => 'required|string|max:10'
        ];
    }
}
