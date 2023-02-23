<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\ValidationFormRequestAPI;

class UpdateProductRequest extends ValidationFormRequestAPI
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
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:498',
            'unit_price' => 'nullable|numeric',
            'in_stock' => 'nullable|numeric',
            'image' => 'nullable',
            'image.*' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'category_id' => 'nullable|numeric'
        ];
    }
}
