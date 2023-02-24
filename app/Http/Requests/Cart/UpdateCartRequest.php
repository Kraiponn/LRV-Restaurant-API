<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\ValidationFormRequestAPI;

class UpdateCartRequest extends ValidationFormRequestAPI
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
            'user_id' => 'nullable|numeric',
            'product_id' => 'nullable|numeric',
            'quantity' => 'nullable|numeric'
        ];
    }
}
