<?php

namespace App\Http\Requests\Cart;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidationFormRequestAPI;

class CreateCartRequest extends ValidationFormRequestAPI
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
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric'
        ];
    }
}
