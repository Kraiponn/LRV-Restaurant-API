<?php

namespace App\Http\Requests;

use App\Http\Requests\ValidationFormRequestAPI;

class RegisterRequest extends ValidationFormRequestAPI
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
            'name' => 'required',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            // 'password' => 'required|min:5|max:16|confirmed',
            'password' => 'required|min:5|max:16',
            'password_confirmation' => 'required|same:password',
            'device_name' => 'required'
        ];
    }

    // public function messages()
    // {
    //     return [];
    // }
}
