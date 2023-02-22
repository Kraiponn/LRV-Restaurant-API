<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidationFormRequestAPI;

class UpdateAccountRequest extends ValidationFormRequestAPI
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
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'device_name' => 'required'
        ];
    }
}
