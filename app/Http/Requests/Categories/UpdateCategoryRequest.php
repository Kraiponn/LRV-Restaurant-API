<?php

namespace App\Http\Requests;

use App\Http\Requests\ValidationFormRequestAPI;

class UpdateCategoryRequest extends ValidationFormRequestAPI
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string',
            'description' => 'nullable|string|max:255'
        ];
    }
}
